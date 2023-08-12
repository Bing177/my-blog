<?php
namespace app\admin\controller;

use think\Controller;
use app\common\Model\Admin;

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
        $adminID = cookie('admin')['id'];
        $avatar = $this->AdminModel->get($adminID)->value('avatar');
        return $this->fetch('index', [
            'flg' => $flg,
            'title' => '天空之眼',
            'avatar' => $avatar
        ]);
    }
}