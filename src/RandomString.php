<?php

namespace ClickCaptcha;

class RandomString
{
    private static $string = [
        '开', '心', '月', '饼', '地', '球', '媒', '体', '自', '苹', '果', '上', '海', '东', '明'
    ];

    public static function getString()
    {
        return self::$string;
    }

    public static function getRandomString($num, $x_axis, $y_axis, $fontSize)
    {
        $data = [];
        $averageAxis = floor($x_axis / $num);

        for ($i = 0; $i < $num; $i++) {
            $data[$i]['x_axis'] = mt_rand(($i * $averageAxis) + $fontSize, (($i + 1) * $averageAxis) - $fontSize);

            $data[$i]['y_axis'] = mt_rand($fontSize, $y_axis - $fontSize);

            $randomKey = array_rand(self::$string, 1);
            $data[$i]['word'] = self::$string[$randomKey];
            unset(self::$string[$randomKey]);
        }
        return $data;
    }
}