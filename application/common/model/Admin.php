<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\common\validate\Admin as AdminValidate;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Admin extends Model
{
    // 软删除
    use SoftDelete;
    protected $table = 't_admin';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加字段
    protected $append = [
        'username_text'
    ];

    public function getUsernameTextAttr($value, $data)
    {
        return substr_replace($data['username'], '****', 3, 4);
    }

    public function getStatusAttr($value, $data)
    {
        $myGet = [
            0 => '禁用',
            1 => '可用'
        ];
        return $myGet[$value];
    }
    public function getIsSuperAttr($value, $data)
    {
        $myGet = [
            0 => '否',
            1 => '是'
        ];
        return $myGet[$value];
    }

    // 登录验证
    public function login($data)
    {
        $validate = new AdminValidate();
        if (!$validate->scene('login')->check($data)) {
            return $validate->getError();
        }
        return 1;
    }
    // 注册验证
    public function register($data)
    {
        $validate = new AdminValidate();
        if (!$validate->scene('register')->check($data)) {
            return $validate->getError();
        }
        return 1;
    }
    // 更新验证
    public function MyUpdate($data)
    {
        $validate = new AdminValidate();
        if (!$validate->scene('update')->check($data)) {
            return [
                'status' => 0,
                'msg' => $validate->getError()
            ];
        }
        return [
            'status' => 1,
            'msg' => '验证成功'
        ];
    }

    // 发送邮件验证链接【暂未用到】
    public function sendEmailVerificationNotification()
    {
        $verificationUrl = url('user/verify', ['token' => '生成验证令牌'], '', true);
        $to = $this->email;
        $subject = '验证邮件';
        $content = '点击链接完成邮箱验证：' . $verificationUrl;
        $mail = new PHPMailer(true);
        try {
            // 配置SMTP设置
            $mail->isSMTP();
            $mail->Host = 'smtp.qq.com';
            $mail->Port = 465;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = '3162074050@qq.com';
            $mail->Password = 'wcb1771438097';

            // 设置发件人和收件人
            $mail->setFrom('bingwuhu123@gmail.com', '天空之眼');
            $mail->addAddress($to);

            // 设置邮件内容
            $mail->Subject = $subject;
            $mail->Body = $content;

            // 发送邮件
            $mail->send();
        } catch (Exception $e) {
            // echo '错误信息：' . $mail->ErrorInfo;
            return ['status' => 0, 'msg' => $mail->ErrorInfo];
        }
    }
}