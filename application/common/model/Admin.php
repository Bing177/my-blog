<?php
namespace app\common\Model;

use think\Model;
use think\model\concern\SoftDelete;
use app\common\validate\Admin as AdminValidate;

class Admin extends Model
{
    // 软删除
    // use SoftDelete;
    protected $table = 't_admin';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

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
}