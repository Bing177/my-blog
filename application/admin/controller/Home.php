<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Admin;

class Home extends Controller
{
    protected $AdminModel = null;
    public function initialize()
    {
        parent::initialize();
        $this->AdminModel = new Admin();
    }
    public function index()
    {
        $flg = isAuth();
        $adminID = (int) cookie('admin')['id'];
        $avatar = $this->AdminModel->get($adminID)['avatar'];
        return $this->fetch('index', [
            'flg' => $flg,
            'title' => '天空之眼',
            'avatar' => $avatar
        ]);
    }
}