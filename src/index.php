<?php

require_once __DIR__ . '/../vendor/autoload.php';

$obj = new \ClickCaptcha\Captcha();
// $obj->output();

$obj->getInline();

$code = $obj->getCode();
echo '<pre>';
print_r($code);die;