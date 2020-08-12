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

    public function drawWord()
    {
        $randomString = RandomString::getRandomString($this->wordsNum, $this->bgWidth, $this->bgHeight, $this->fontSize);
        $_SESSION['random_string'] = array_column($randomString, 'word');

        shuffle($randomString);

        pd($_SESSION['random_string'], $randomString);
        foreach ($randomString as $k => $v) {
            $randAngle = mt_rand(0, 90);

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