<?php
namespace app\admin\controller;

use think\captcha\Captcha;

class MyCaptcha extends Captcha
{
    // 重写验证方法，不区分大小写
    public function check($code, $id = '')
    {
        $code = strtolower($code);
        return parent::check($code, $id);
    }
}