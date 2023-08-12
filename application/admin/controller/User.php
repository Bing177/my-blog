<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Admin;

class User extends Controller
{
    // 定义User模型
    protected $UserModel = null;

    // 初始化
    public function initialize()
    {
        parent::initialize();
        // 初始化User模型
        $this->UserModel = new Admin();
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
            $adminID = cookie('admin')['id'];
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
                $data['avatar'] = 'http://' . $_SERVER['SERVER_NAME'] . "/" . $imgResult['file'];
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
                return ['status' => 1, 'msg' => '数据修改成功'];
            } else {
                return ['status' => 0, 'msg' => $this->UserModel->getError()];
            }
        }

    }
}