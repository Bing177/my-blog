<?php
namespace app\helper;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;
use think\Config;

class EmailHelper
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }
    public function sendEmail($to, $subject, $body)
    {
        $config = $this->config->get('email');
        // 创建一个 PHPMailer 实例 $mail
        $mail = new PHPMailer(true);
        try {
            // 设置超时时间
            $mail->Timeout = $config['timeout'];

            // 设置 SMTP 地址和端口
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->Port = $config['port'];

            // SSL/TLS 加密
            $mail->SMTPSecure = 'ssl';

            // 设置邮箱账号和密码[使用授权码]
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPAuth = true;

            // 设置发件人信息
            $mail->setFrom($config['from_address'], $config['from_name']);

            // 添加收件人地址
            $mail->addAddress($to);

            // 设置主题和正文
            $mail->Subject = $subject;
            $mail->isHTML(true); // 设置邮件的 Content-Type 为 text/html
            $mail->Body = $body;

            // 发送邮件
            $mail->send();
            return '邮件发送成功';
        } catch (Exception $e) {
            // 捕获超时异常
            if ($e instanceof Exception && $e->getCode() === 28) {
                return '邮件发送超时';
            }
            return '邮件发送异常:' . $e->getMessage();
        }
    }
}