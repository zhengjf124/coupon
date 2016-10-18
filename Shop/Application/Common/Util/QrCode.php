<?php
namespace Common\Util;

require_once(APP_PATH . 'Common/Util/endroid/qrcode/src/QrCode.php');
use Endroid\QrCode\QrCode as code;

class QrCode
{
    private $url;
    private $path;

    /***
     * QrCode constructor.
     * @param $url string 需要保存到二维码中的地址
     * @param $path string 生成的二维码的路径
     */
    public function __construct($url, $path)
    {
        if (!isset($url) || !isset($path))
            return false;

        $this->url = $url;
        $this->path = $path;
    }

    public function setQrCode($size = 300, $label = '')
    {
        $qrCode = new code();
        $qrCode
            //->setLogo('Public/upload/test/pic_photo1.png')
            //->setLogoSize(85)
            ->setText($this->url)
            ->setSize($size)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel($label)
            ->setLabelFontSize(16)
            ->setImageType(code::IMAGE_TYPE_PNG);

        // now we can directly output the qrcode
        //header('Content-Type: ' . $qrCode->getContentType());
        //$qrCode->render();
        if ($this->makeDir(dirname($this->path))) {
            if ($fp = fopen($this->path, "w")) {
                if (@fwrite($fp, $qrCode->get())) {
                    fclose($fp);
                    return true;
                } else {
                    fclose($fp);
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * 连续创建目录
     * @param string $dir 目录字符串
     * @param string $mode 权限数字
     * @return boolean
     */
    private function makeDir($dir, $mode = "0754")
    {
        if (!$dir) return false;

        if (!file_exists($dir)) {
            return mkdir($dir, $mode, true);
        } else {
            return true;
        }

    }
}