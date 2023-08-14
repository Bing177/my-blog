<?php
namespace app\helper;

use \PHPMailer\PHPMailer\PHPMailer;
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

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->Port = $config['port'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->setFrom($config['from_address']);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;

        if ($mail->send())
            return true;
        else
            return $mail->ErrorInfo;
    }
}