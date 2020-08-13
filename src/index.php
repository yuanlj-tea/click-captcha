<?php

require_once __DIR__ . '/../vendor/autoload.php';

$obj = new \ClickCaptcha\Captcha();
// $obj->output();

$obj->getInline();

$obj->output();