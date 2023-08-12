<?php
namespace app\admin\controller;

use think\Controller;

class Captcha extends Controller
{
    // 画布
    protected $img = null;
    // 宽
    protected $width = 123;
    // 高
    protected $height = 38;
    // 背景色
    protected $bgc = null;
    // 文本字体
    protected $fontfamily = "C:/Windows/Fonts/CENTURY.TTF";
    // 字体大小
    protected $fontsize = 19;

    public function genCode()
    {
        session_start();
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $this->bgc = imagecolorallocate($this->img, 255, 243, 201);
        imagefilledrectangle($this->img, 0, 0, $this->width, $this->height, $this->bgc);
        $char1 = generatorChar(1);
        $char2 = generatorChar(1);
        $char3 = generatorChar(1);
        $char4 = generatorChar(1);
        $_SESSION['code'] = $char1 . $char2 . $char3 . $char4;

        imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), 10, 26, imagecolorallocate($this->img, rand(0, 255), rand(0, 255), rand(0, 255)), $this->fontfamily, $char1);
        imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), 48, 26, imagecolorallocate($this->img, rand(0, 255), rand(0, 255), rand(0, 255)), $this->fontfamily, $char2);
        imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), 68, 26, imagecolorallocate($this->img, rand(0, 255), rand(0, 255), rand(0, 255)), $this->fontfamily, $char3);
        imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), 88, 26, imagecolorallocate($this->img, rand(0, 255), rand(0, 255), rand(0, 255)), $this->fontfamily, $char4);
        for ($i = 0; $i < 20; $i++)
            imagesetpixel($this->img, rand(5, 115), rand(5, 35), imagecolorallocate($this->img, rand(0, 255), rand(0, 255), rand(0, 255)));
        for ($i = 0; $i < 5; $i++)
            imageline($this->img, rand(10, 110), rand(5, 35), rand(50, 110), rand(5, 35), imagecolorallocate($this->img, rand(0, 255), rand(0, 255), rand(0, 255)));
        header("Content-Type:image/png");
        imagepng($this->img);
        imagedestroy($this->img);
    }

}