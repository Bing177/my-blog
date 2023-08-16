<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
if (!function_exists('generatorChar')) {
    /**
     * 获取随机字符串
     *  @param int $length 随机字符串长度
     */
    function generatorChar($length = 4)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $captcha = '';
        for ($i = 0; $i < $length; $i++)
            $captcha .= $characters[rand(0, strlen($characters) - 1)];
        return $captcha;
    }
}

if (!function_exists('isAuth')) {
    /**
     * 检查是否登录
     * @return boolean
     */
    function isAuth()
    {
        $isAuth = cookie('loginInfo');
        if ($isAuth)
            return true;
        else
            return false;
    }
}

if (!function_exists('imageRelativePath')) {
    /**
     * @param resource $file 上传的文件
     * @param int $size 文件大小
     * @param string $type 文件类型
     * @return mixed
     */
    function imageRelativePath($file = null, $size = 102400, $type = "jpg,png,jpeg,gif")
    {
        if ($file) {
            $info = $file->validate(['size' => $size, 'ext' => $type])->move('public/upload');
            if ($info) {
                $filePath = $info->getPathName();
                $filePath = str_replace('public', '', $filePath);
                return ['code' => 1, 'msg' => '上传成功', 'file' => $filePath];
            } else {
                return ['code' => 0, 'msg' => $file->getError()];
            }
        } else {
            return ['code' => 0, 'message' => '没有文件被上传'];
        }
    }
}