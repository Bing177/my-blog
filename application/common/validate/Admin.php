<?php

namespace app\common\validate;

use think\Validate;

class Admin extends Validate
{
    /* 验证规则 */
    protected $rule = [
        'username' => ['require', 'regex:/^1[3|5|7|8|9]\d{9}$/'],
        'password' => ['require', 'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9]{6,20}$/'],
        'repass' => ['require', 'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9]{6,20}$/'],
        'nickname' => ['require', 'regex:/^(?!\s*$).{1,20}$/'],
        'code' => 'require|captcha',
        'email' => ['regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
    ];
    /* 提示消息 */
    protected $message = [
        'username.require' => '用户名不能为空',
        'username.regex' => '用户名格式有误',
        'password.require' => '密码不能为空',
        'password.regex' => '密码格式有误',
        'repass.require' => '确认密码不能为空',
        'repass.regex' => '确认密码格式有误',
        'nickname.require' => '昵称不能为空',
        'nickname.regex' => '昵称格式有误',
        'code.require' => '验证码不能为空',
        'code.captcha' => '验证码有误',
        'email.regex' => '邮箱格式有误',
    ];
    /* 验证场景 */
    public function sceneLogin()
    {
        return $this->only(['username', 'password'])->remove('password', 'regex');
    }
    public function sceneRegister()
    {
        return $this->only(['username', 'password', 'repass', 'code']);
    }
    public function sceneUpdate()
    {
        return $this->only(['username', 'password', 'nickname', 'email'])->remove('password', 'require');
    }
}