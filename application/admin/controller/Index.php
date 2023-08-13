<?php

namespace app\admin\controller;

use think\Controller;
use think\facade\Request;
use think\captcha\Captcha;

class Index extends Controller
{
    protected $AdminModel = null;
    public function initialize()
    {
        // 加载模型
        $this->AdminModel = model('Admin');

    }

    public function index()
    {
        return $this->AdminModel->select();
    }
    //后台登录
    public function login()
    {
        if ($this->request->isPost()) {
            $data = [
                'username' => $this->request->param('username', '', 'trim'),
                'password' => $this->request->param('password', '', 'trim')
            ];

            $res = model('Admin')->login($data);
            if ($res != 1) {
                return [
                    'code' => 0,
                    'msg' => $res,
                    'data' => null,
                    'wait' => 3
                ];
            }
            $userinfo = $this->AdminModel->where('username', $data['username'])->find();
            if (!$userinfo) {
                $this->error('账号未注册');
            }
            $salt = $userinfo->value('salt');
            if (md5($data['password'] . $salt) != $userinfo['password']) {
                $this->error('密码有误');
            }
            $user = [
                'id' => $userinfo['id'],
                'username' => $userinfo['username'],
                'nickname' => $userinfo['nickname']
            ];
            cookie('admin', $user);
            $this->success('登录成功', url('/admin/home'));

        }
        return $this->fetch('login', ['title' => '登录']);
    }

    // 生成图片验证码
    public function genCaptcha()
    {
        // 验证码参数配置
        $config = [
            'expire' => 300,
            'fontSize' => 17,
            'imageW' => 120,
            'length' => 4,
            'bg' => [255, 243, 201]
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
    // 后台注册
    public function register()
    {
        if ($this->request->isPost()) {
            $data = [
                'username' => $this->request->param('username', '', 'trim'),
                'password' => $this->request->param('password', '', 'trim'),
                'repass' => $this->request->param('repass', '', 'trim'),
                'code' => $this->request->param('code', '', 'trim')
            ];
            $res = model('Admin')->register($data);
            if ($res != 1) {
                return [
                    'code' => 0,
                    'msg' => $res,
                    'data' => null,
                    'wait' => 3
                ];
            }

            if ($data['password'] != $data['repass']) {
                return $this->error('密码与确认密码不一致');
            }
            // 生成密码盐
            $salt = generatorChar(10);
            // 生成默认昵称
            $nickname = generatorChar(7);
            // 封装数据
            $admin = [
                'username' => $data['username'],
                'password' => md5($data['password'] . $salt),
                'salt' => $salt,
                'nickname' => $nickname,
                'email' => null,
                'status' => 0,
                'is_super' => 0
            ];
            // 添加默认头像
            $admin['avatar'] = 'https://pic.imgdb.cn/item/64ad70fc1ddac507cc5629ae.png';
            $existUsername = $this->AdminModel->where('username', $data['username'])->find();
            if ($existUsername) {
                return $this->error('用户名已注册');
            }
            $res = $this->AdminModel->data($admin)->save();
            if (!$res) {
                return $this->error('注册失败');
            }
            return $this->success('注册成功', url('admin/index/login'));
        }
        return $this->fetch('register', ['title' => '注册']);
    }
}