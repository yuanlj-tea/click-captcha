<?php

namespace ClickCaptcha;

class Captcha
{
    /**
     * 背景图片宽
     * @var int
     */
    private $bgWidth = 240;

    /**
     * 背景图片高
     * @var int
     */
    private $bgHeight = 150;


    private $imgBg = null;

    /**
     * 字体大小
     * @var int
     */
    private $fontSize = 20;

    /**
     * 字体路径
     * @var string
     */
    private $fontPath = __DIR__ . '/font/zhankukuhei.ttf';

    /**
     * Allowed image types for the background images
     *
     * @var array
     */
    protected $allowedBackgroundImageTypes = array('image/png', 'image/jpeg', 'image/gif');

    /**
     * 字数
     * @var int
     */
    private $wordsNum = 4;

    /**
     * 随机的背景图片路径
     * @var
     */
    protected $randomBg;

    /**
     * 随机的汉字
     * @var
     */
    protected $randomString;

    private $noiseLevel = 3;

    private $imgLogo;

    public function __construct()
    {
        // start session if is not started already
        if (false === headers_sent() && '' === session_id()) {
            session_start();
        }
        $this->getRandomBg();
    }

    protected function getRandomBg()
    {
        $img = glob(__DIR__ . '/image/*');
        $randomKey = array_rand($img, 1);
        $this->randomBg = $img[$randomKey];
    }

    public function createBackgroundImage()
    {
        $this->createImg();

        // for ($i = 0; $i < 5; $i++) {
        //     $this->drawLine();
        // }

        // $this->drawSinLine($this->bgWidth / 2);

        $this->drawPoint();

        $this->drawArc();

        $this->drawNoise();

        $this->drawWord();

        header('Content-Type: image/png');
        imagepng($this->imgBg);
    }

    public function createImg()
    {
        $imgMimeType = $this->validateBackgroundImage($this->randomBg);
        $this->imgBg = $this->createBackgroundImageFromType($this->randomBg, $imgMimeType);

        $imgWidth = imagesx($this->imgBg);
        $imgHeight = imagesy($this->imgBg);

        $this->fontSize = $this->fontSize * ($imgWidth / $this->bgWidth);

        $this->bgWidth = $imgWidth;
        $this->bgHeight = $imgHeight;
    }

    private function drawLogo()
    {
        $logoPath = __DIR__ . '/logo/ky-logo2.png';
        $logoMimeType = $this->validateBackgroundImage($logoPath);
        $this->imgLogo = $this->createBackgroundImageFromType($logoPath, $logoMimeType);

        $logoWidth = imagesx($this->imgLogo);
        $logoHeight = imagesy($this->imgLogo);

        list($srcX, $srcY) = $this->getLogoSrcPos();

        // imagecopyresampled ($this->imgBg,$this->imgLogo,0,0,0,0,$this->bgWidth,$this->bgHeight,imagesx($this->imgLogo),imagesy($this->imgLogo));
        imagecopymerge($this->imgBg, $this->imgLogo, $srcX, $srcY, 0, 0, $logoWidth, $logoHeight, 20);
    }

    private function getLogoSrcPos()
    {
        $centreX = $this->bgWidth / 2;
        $centreY = $this->bgHeight / 2;

        $logoWidth = imagesx($this->imgLogo);
        $logoHeight = imagesy($this->imgLogo);

        $srcX = $centreX - $logoWidth / 2;
        $srcY = $centreY - $logoHeight / 2;

        return [$srcX, $srcY];
    }

    /**
     * 画杂点
     * 往图片上写不同颜色的字母或数字
     */
    private function drawNoise()
    {
        $codeSet = '2345678abcdefhijkmnpqrstuvwxyz';
        for ($i = 0; $i < $this->noiseLevel; $i++) {
            //杂点颜色
            $noiseColor = $this->createColor();
            for ($j = 0; $j < 5; $j++) {
                // 绘杂点
                imagestring($this->imgBg, $this->fontSize / 2, mt_rand(-10, $this->bgWidth), mt_rand(-10, $this->bgHeight), $codeSet[mt_rand(0, 29)], $noiseColor);
            }
        }
    }

    /**
     * 画正弦干扰线
     * @param $w
     */
    private function drawSinLine($w)
    {
        $h = $this->bgHeight;
        $h1 = rand(-5, 5);
        $h2 = rand(-1, 1);
        $w2 = rand(10, 15);
        $h3 = rand(4, 6);

        $color = $this->createColor();

        for ($i = -$w / 2; $i < $w / 2; $i = $i + 0.1) {
            $y = $h / $h3 * sin($i / $w2) + $h / 2 + $h1;
            imagesetpixel($this->imgBg, $i + $w / 2, $y, $color);
            $h2 != 0 ? imagesetpixel($this->imgBg, $i + $w / 2, $y + $h2, $color) : null;
        }
    }

    private function drawPoint()
    {
        for ($i = 0; $i < $this->bgWidth; $i++) {
            imagesetpixel($this->imgBg, rand(1, $this->bgWidth - 1), rand(1, $this->bgHeight - 1), $this->createColor());
        }
    }

    private function drawArc()
    {
        imagearc($this->imgBg, mt_rand(-$this->bgWidth, $this->bgWidth), mt_rand(-$this->bgHeight, $this->bgHeight), mt_rand(0, $this->bgWidth), mt_rand(0, $this->bgHeight), mt_rand(0, 360), mt_rand(0, 360), $this->createColor());
    }

    protected function createColor()
    {
        $red = mt_rand(100, 255);
        $green = mt_rand(100, 255);
        $blue = mt_rand(100, 255);

        $color = imagecolorallocate($this->imgBg, $red, $green, $blue);
        return $color;
    }

    public function drawLine()
    {
        $color = $this->createColor();

        // Horizontal
        if (mt_rand(0, 1)) {
            $Xa = mt_rand(0, $this->bgWidth / 2);
            $Ya = mt_rand(0, $this->bgHeight);
            $Xb = mt_rand($this->bgWidth / 2, $this->bgWidth);
            $Yb = mt_rand(0, $this->bgHeight);
        } else { // Vertical
            $Xa = mt_rand(0, $this->bgWidth);
            $Ya = mt_rand(0, $this->bgHeight / 2);
            $Xb = mt_rand(0, $this->bgWidth);
            $Yb = mt_rand($this->bgHeight / 2, $this->bgHeight);
        }
        imagesetthickness($this->imgBg, mt_rand(1, 3));
        imageline($this->imgBg, $Xa, $Ya, $Xb, $Yb, $color);
    }

    public function drawWord()
    {
        $this->randomString = RandomString::getRandomString($this->wordsNum, $this->bgWidth, $this->bgHeight, $this->fontSize);

        $_SESSION['random_string'] = array_slice($this->randomString, 0, $this->wordsNum - 1);

        shuffle($this->randomString);

        // file_put_contents('./a.log',print_r($_SESSION['random_string'],true));

        foreach ($this->randomString as $k => $v) {
            $randAngle = mt_rand(-30, 30);

            $color = imagecolorallocate($this->imgBg, rand(40, 140), rand(40, 140), rand(40, 140));

            imagettftext($this->imgBg, $this->fontSize, $randAngle, $v['x_axis'], $v['y_axis'], $color, $this->fontPath, $v['word']);
        }
    }

    /**
     * Validate the background image path. Return the image type if valid
     *
     * @param string $backgroundImage
     * @return string
     * @throws Exception
     */
    protected function validateBackgroundImage($backgroundImage)
    {
        // check if file exists
        if (!file_exists($backgroundImage)) {
            $backgroundImageExploded = explode('/', $backgroundImage);
            $imageFileName = count($backgroundImageExploded) > 1 ? $backgroundImageExploded[count($backgroundImageExploded) - 1] : $backgroundImage;

            throw new \Exception('Invalid background image: ' . $imageFileName);
        }

        // check image type
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $imageType = finfo_file($finfo, $backgroundImage);
        finfo_close($finfo);

        if (!in_array($imageType, $this->allowedBackgroundImageTypes)) {
            throw new \Exception('Invalid background image type! Allowed types are: ' . join(', ', $this->allowedBackgroundImageTypes));
        }

        return $imageType;
    }

    /**
     * Create background image from type
     *
     * @param string $backgroundImage
     * @param string $imageType
     * @return resource
     * @throws Exception
     */
    protected function createBackgroundImageFromType($backgroundImage, $imageType)
    {
        switch ($imageType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($backgroundImage);
                break;
            case 'image/png':
                $image = imagecreatefrompng($backgroundImage);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($backgroundImage);
                break;

            default:
                throw new \Exception('Not supported file type for background image!');
                break;
        }

        return $image;
    }

    public function __destruct()
    {
        imagedestroy($this->imgBg);
    }
}