<?php

namespace ClickCaptcha;

class RandomString
{
    protected static $instance;

    protected $string = [
        '开', '心', '月', '饼', '地', '球', '媒', '体', '自', '苹', '果', '上', '海', '东', '明'
    ];

    protected function getRandomString($num, $x_axis, $y_axis, $fontSize, $stringLib = '')
    {
        $this->string = !empty($stringLib) ? str_split($stringLib, 3) : $this->string;

        $data = [];

        $averageAxis = floor($x_axis / $num);

        for ($i = 0; $i < $num; $i++) {
            $data[$i]['x_axis'] = mt_rand(($i * $averageAxis) + $fontSize, (($i + 1) * $averageAxis) - $fontSize - 10);

            $data[$i]['y_axis'] = mt_rand($fontSize + 10, $y_axis - $fontSize - 10);

            $radius = $fontSize / 2;
            $data[$i]['scope'] = [
                'x_limit_left' => $data[$i]['x_axis'] - $radius,
                'x_limit_right' => $data[$i]['x_axis'] + $fontSize,
                'y_limit_up' => $data[$i]['y_axis'] - $fontSize,
                'y_limit_down' => $data[$i]['y_axis'] + 0,
            ];

            $randomKey = array_rand($this->string, 1);

            $data[$i]['word'] = $this->string[$randomKey];

            unset($this->string[$randomKey]);
        }
        return $data;
    }

    public static function __callStatic($name, $arguments)
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return call_user_func_array([static::$instance, $name], $arguments);
    }

}