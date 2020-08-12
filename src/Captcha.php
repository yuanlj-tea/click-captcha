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

        for ($i = 0; $i < 5; $i++) {
            $this->drawLine();
        }
        $this->drawSinLine($this->bgWidth/2);

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

    //画正弦干扰线
    private function drawSinLine($w)
    {
        $h = $this->bgHeight;
        $h1 = rand(-5, 5);
        $h2 = rand(-1, 1);
        $w2 = rand(10, 15);
        $h3 = rand(4, 6);

        $red = mt_rand(100, 255);
        $green = mt_rand(100, 255);
        $blue = mt_rand(100, 255);

        $color = imagecolorallocate($this->imgBg, $red, $green, $blue);

        for ($i = -$w / 2; $i < $w / 2; $i = $i + 0.1) {
            $y = $h / $h3 * sin($i / $w2) + $h / 2 + $h1;
            imagesetpixel($this->imgBg, $i + $w / 2, $y, $color);
            $h2 != 0 ? imagesetpixel($this->imgBg, $i + $w / 2, $y + $h2, $color) : null;
        }
    }

    public function drawLine()
    {
        $red = mt_rand(100, 255);
        $green = mt_rand(100, 255);
        $blue = mt_rand(100, 255);

        $color = imagecolorallocate($this->imgBg, $red, $green, $blue);

        if (mt_rand(0, 1)) { // Horizontal
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