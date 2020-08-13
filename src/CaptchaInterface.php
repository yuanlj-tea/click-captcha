<?php


namespace ClickCaptcha;


interface CaptchaInterface
{
    /**
     * 创建验证图片
     * @return mixed
     */
    public function create();

    /**
     * 显示验证码图片
     */
    public function output();

    /**
     * 获取验证码的base 64数据
     * @return mixed
     */
    public function getInline();

    /**
     * 获取验证码内容
     */
    public function getCode();

    /**
     * 校验用户输入的内容
     * @return mixed
     */
    public function check($param);
}