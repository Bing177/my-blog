<?php
namespace app\admin\controller;

use app\common\model\Emails;
use think\Config;
use think\Controller;
use app\common\model\Admin;
use app\helper\EmailHelper;

class User extends Controller
{
    // 定义User模型
    protected $UserModel = null;

    // 定义Emails模型
    protected $EmailsModel = null;

    // 定义cookie中admin的id
    protected $adminID = null;

    // 初始化
    public function initialize()
    {
        parent::initialize();
        // 初始化User模型和Emails模型
        $this->UserModel = new Admin();
        $this->EmailsModel = new Emails();

        // 初始化cookie中的admin的id
        $this->adminID = cookie('admin')['id'];
    }
    public function index()
    {
        // 获取cookie登录信息   
        $adminInfo = cookie('admin');
        // 获取ID
        $adminID = $adminInfo['id'];
        // 查找t_admin表
        $res = $this->UserModel->find($adminID);
        if (!$res) {
            $this->error($this->UserModel->getError());
        }
        return $this->fetch('profile', [
            'profile' => $res,
            'title' => '我的',
            'validateRes' => null
        ]);
    }

    // 更新管理员信息
    public function update()
    {
        if ($this->request->isPost()) {
            // 获取本地缓存信息
            $adminID = $this->adminID;
            if (!$adminID) {
                return ['status' => 0, 'msg' => 'admin信息失效，请重新登录'];
            }
            // 获取表单信息
            $data = [
                'username' => $this->request->param('username', '', 'trim'),
                'nickname' => $this->request->param('nickname', '', 'trim'),
                'email' => $this->request->param('email', '', 'trim'),
                'status' => $this->request->param('status', 1),
                'is_super' => $this->request->param('privilege', 0)
            ];
            // 判断密码是否为空
            if (!empty($this->request->param('password', '', 'trim')))
                $data['password'] = $this->request->param('password', '', 'trim');
            /* 头像处理 */
            try {
                // 获取头像
                $file = $this->request->file('avatar');
                // 调用生成图片相对路径方法
                $imgResult = imageRelativePath($file, 1024000, 'jpeg,jpg,png,gif,webp');
                if ($imgResult['status'] != 1) { // 获取头像失败
                    return $imgResult;
                }
                $data['avatar'] = 'http://' . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/" . $imgResult['file'];
            } catch (\Exception $e) {
                return json(['status' => 0, 'msg' => '未选择图片']);
            }

            /* 验证 */
            // 封装验证数据
            $validateData = [
                'username' => $this->request->param('username', '', 'trim'),
                'password' => $this->request->param('password', '', 'trim'),
                'nickname' => $this->request->param('nickname', '', 'trim'),
                'email' => $this->request->param('email', '', 'trim')
            ];
            $validateResult = $this->UserModel->MyUpdate($validateData);
            if (!$validateResult['status']) { // 验证失败
                return $validateResult;
            }


            // 更新数据
            $updateResult = $this->UserModel->isUpdate(true)->update($data, ['id' => $adminID]);
            if ($updateResult) {
                // 判断是否输入邮箱
                if (!empty($data['email'])) {
                    // 邮箱插入到emails表中
                    $emailResult = $this->EmailsModel->save([
                        'email' => $data['email'],
                        'admin_id' => $adminID
                    ]);
                    if (!$emailResult) {
                        return ['status' => 0, 'msg' => $this->EmailsModel->getError()];
                    }
                    return ['status' => 1, 'msg' => '数据修改成功'];
                }
            } else {
                return ['status' => 0, 'msg' => $this->UserModel->getError()];
            }
        }

    }

    // 邮箱认证码发送
    public function sendMail(Config $config)
    {
        if ($this->request->isPost()) {
            $emailHelper = new EmailHelper($config);
            $data = $this->request->param();
            $code = '';
            $timeout = $config->get('email')['timeout'] / 60;
            for ($i = 0; $i < 6; $i++) {
                $code .= rand(0, 9);
            }
            $to = $data['email'];
            $subject = '邮箱认证';
            $body = "<h3>欢迎来到天空之眼个人博客</h3><hr/>
                    <p>还差一步就完成邮箱验证，这是验证码：
                        <a href='javascript:void(0);' style='color: rgb(26, 225, 0);'>$code</a>，
                        ($timeout 分钟有效) 切勿将验证码告诉他人，若非本人操作请忽略该邮件。
                    </p>
            ";
            if ($emailHelper->sendEmail($to, $subject, $body)) {
                // 获取cookie中的admin的id
                $adminId = $this->adminID;

                // 设置有效期
                $expire = time() + $config->get('email')['timeout'];

                // 将有效期插入到email中
                $expireResult = $this->EmailsModel->where('admin_id', $adminId)->find()->save([
                    'expire' => date('Y-m-d H:i:s', $expire)
                ]);
                if (!$expireResult) {
                    return ['status' => 0, 'msg' => $this->EmailsModel->getError()];
                }
                // 将认证码插入到数据库中
                $result = $this->UserModel->get($adminId)->isUpdate(true)->save(['code' => $code]);
                if (!$result) {
                    return ['status' => 0, 'msg' => $this->UserModel->getError()];
                }

                return ['status' => 1, 'msg' => '邮箱发送成功，请注意查收'];
            } else {
                return ['status' => 0, 'msg' => '发送失败：' . $emailHelper->sendEmail($to, $subject, $body)];
            }
        }
    }

    // 邮箱认证
    public function verifyMail(Config $config)
    {
        if ($this->request->isPost()) {
            // 获取cookie中的admin的id
            $adminID = $this->adminID;
            // 获取当前时间戳
            $now = time();
            // 获取emails中的expire
            $expire = $this->EmailsModel->get($adminID)['expire'];
            if (empty($expire)) {
                return ['status' => 0, 'msg' => '有效期为空'];
            }
            // 判断是否过期
            if ($now > strtotime($expire)) {
                // 已过期
                return ['status' => 0, 'msg' => '认证码已过期，请重新获取'];
            }

            // 获取数据库中的认证码
            $code = $this->UserModel->get($adminID)['code'];
            if (!$code) {
                return ['status' => 0, 'msg' => '认证码不存在'];
            }
            // 获取用户输入的代码
            $ucode = $this->request->param('code', '', 'trim');
            if ($code != $ucode) {
                return ['status' => 0, 'msg' => '验证码错误'];
            }

            // 更新admin中的auth
            $verifyResult = $this->UserModel->get($adminID)->isUpdate(true)->save(['auth' => '1']);
            if (!$verifyResult) {
                return ['status' => 0, 'msg' => $this->UserModel->getError()];
            }
            return ['status' => 1, 'msg' => '邮箱认证成功'];
        }
    }
}