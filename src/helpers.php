<?php
/**
 * 全局函数
 * @author chenzhangwei
 */

if (!function_exists('app_name')) {
    /**
     * 获取网站名称
     * @return mixed
     */
    function app_name()
    {
        return env('SITE_NAME');
    }
}

if (!function_exists('get_process_state_conf')) {
    /**
     * 获取流程状态配置
     * @return array
     */
    function get_process_state_conf()
    {
        return json_encode(array(
            1 => "有效",
            2 => "无效",
            3 => "测试"
        ));
    }
}

if (!function_exists('get_process_node_type_conf')) {
    /**
     * 获取流程节点类型配置
     * 流程只能第一个节点类型是“create”，最后一个节点类型是“archive”。中间节点不能存在它们.
     * @return json
     */
    function get_process_node_type_conf()
    {
        return json_encode(array(
            "create" => "创建",
            //"submit" => "提交",//按步骤设置编号流转，没有审核意见按钮
            "approved" => "审核",
            "archive" => "归档"
        ));
    }
}


if (!function_exists('get_process_node_attr_conf')) {
    /**
     * 获取流程节点属性配置
     * @return json
     */
    function get_process_node_attr_conf()
    {
        return json_encode(array(
            "general" => "非会签",
            "countersign" => "会签"
        ));
    }
}


if (!function_exists('get_process_node_optgroup_conf')) {
    /**
     * 获取流程节点操作组类型配置
     * @return json
     */
    function get_process_node_optgroup_conf()
    {
        return json_encode(array(
            "0" => "全部",
            "1" => "部门",
            "2" => "角色",
            "3" => "个人",
            "4" => "表单赋值",
            "5" => "并行",
            "6" => "直属上级",
            "7" => "逐层审批",
            "8" => "指定部门的负责人",
            "9" => "部门负责人(3级或以上)",
            "10" => "指定流程节点审批人"
        ));
    }
}

if (!function_exists('get_upload_file_type_conf')) {
    /**
     * 获取流程节点操作组类型配置
     * @return array
     */
    function get_upload_file_type_conf()
    {
        return array(
            "rar" => "rar",
            "zip" => "zip",
            "docx" => "docx",
            "doc" => "doc",
            "xlsx" => "xlsx",
            "xls" => "xls",
            "png" => "png",
            "pdf" => "pdf",
            "txt" => "txt",
            "jpg" => "jpg",
            "gif" => "gif",
            "mp4" => "mp4",
            "wma" => "wma",
            "rmba" => "rmba",
            "JPEG" => "JPEG",
            "EXIF" => "EXIF",
            "jpeg" => "jpeg",
            "PNG" => "PNG",
            "JPG" => "JPG",
            "ppt" => "ppt",
            "pptx" => "pptx",
        );
    }
}

if (!function_exists('get_workflow_button_conf')) {
    /**
     * 获取流程节点操作组类型配置
     * @return array
     */
    function get_workflow_button_conf()
    {
        return array(
            "save" => "保存",
            "submit" => "提交",
            "delete" => "删除",
            "cancel" => "撤回",
            "forward" => "转发",
            "invalid" => "作废",
            "back" => "退回申请人",
            "consult" => "征询",
            "print" => "打印"
        );
    }
}


if (!function_exists('xss_filter')) {
    /**
     * xss过滤
     * @param array $input 需要过滤的数组
     * @return array
     */
    function xss_filter($input)
    {
        if (is_array($input)) {
            if (sizeof($input)) {
                foreach ($input as $key => $value) {
                    if (is_array($value) && sizeof($value)) {
                        $input[$key] = xss_filter($value);
                    } else {
                        if (!empty($value)) {
                            $input[$key] = htmlentities($value, ENT_QUOTES, 'UTF-8');
                        }
                    }
                }
            }
            return $input;
        }
        return htmlentities($input, ENT_QUOTES, 'UTF-8');
    }
}


if (!function_exists('get_dept_leader_by_R2008')) {
    /**
     * 获取影游事业部领导
     * @return array
     */
    function get_dept_leader_by_R2008()
    {
        return [
            ['USR_UID' => '58505055056ab1b2feb6343097106104', 'USR_CN' => '罗浩'],
            ['USR_UID' => '25999454556ab1b1b7bc348002577465', 'USR_CN' => '杨友发'],
        ];
    }
}

if (!function_exists('verify_signature')) {
    /**
     * 验证签名
     * @param string
     */
    function verify_signature($clientSign, $serverData, $key)
    {
        $srvSign = hash_hmac('sha256', $serverData, $key);
        if ($srvSign === $clientSign) {
            return true;
        }
        return false;
    }
}

if (!function_exists('verify_signature_by_md5')) {
    /**
     * 验证签名md5法
     * @param string
     */
    function verify_signature_by_md5($clientSign, $serverData, $key)
    {
        $srvSign = md5($serverData . $key);
        if ($srvSign === $clientSign) {
            return true;
        }
        return false;
    }
}

if (!function_exists('digital_amount_to_cny')) {
    /**
     * 数字金额转大写人民币
     * @param string
     */
    function digital_amount_to_cny($num)
    {
        $capUnit = array('万', '亿', '万', '圆', '');
        $capDigit = array(2 => array('角', '分', ''), 4 => array('仟', '佰', '拾', ''));
        $capNum = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
        if ((strpos(strval($num), '.') > 16) || (!is_numeric($num)))
            return '';
        $num = sprintf("%019.2f", $num);
        $CurChr = array('', '');
        for ($i = 0, $ret = '', $j = 0; $i < 5; $i++, $j = $i * 4 + floor($i / 4)) {
            $nodeNum = substr($num, $j, 4);
            for ($k = 0, $subret = '', $len = strlen($nodeNum); (($k < $len) && (intval(substr($nodeNum, $k)) != 0)); $k++) {
                $CurChr[$k % 2] = $capNum[$nodeNum{$k}] . (($nodeNum{$k} == 0) ? '' : $capDigit[$len][$k]);
                if (!(($CurChr[0] == $CurChr[1]) && ($CurChr[$k % 2] == $capNum[0])))
                    if (!(($CurChr[$k % 2] == $capNum[0]) && ($subret == '') && ($ret == '')))
                        $subret .= $CurChr[$k % 2];
            }
            $subChr = $subret . (($subret == '') ? '' : $capUnit[$i]);
            if (!(($subChr == $capNum[0]) && ($ret == '')))
                $ret .= $subChr;
        }
        $ret = ($ret == "") ? $capNum[0] . $capUnit[3] : $ret;
        return $ret;
    }
}


if (!function_exists('oa_http_get')) {
    /**
     * http get 请求
     * @param string
     */
    function oa_http_get($url)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));
        curl_setopt($ch, CURLOPT_USERAGENT, "(kingnet oa web server)");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $res = curl_exec($ch);
        if (curl_errno($ch) || empty($res)) {
            $str = date('Y-m-d H:i:s') . ':' .
                $url . '--' .
                curl_errno($ch) . '--' .
                json_encode($res) . '--get--' .
                curl_error($ch) . '--' .
                json_encode(curl_getinfo($ch));
            file_put_contents('/data/logs/oa-kingnet/curl_error.log', $str . PHP_EOL, FILE_APPEND);
        }
        curl_close($ch);
        if (env("APP_DEBUG")) {
            system("echo '{$url}---" . print_r($res, true) . "' >>/tmp/curl.log");
        }
        return $res;
    }
}


if (!function_exists('oa_http_post')) {
    /**
     * http post 请求
     * @param string $url 请求网址
     * @param string $data post数据
     */
    function oa_http_post($url, $data, $header = [])
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($header))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        else
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERAGENT, "(kingnet oa web server)");
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $res = curl_exec($ch);
        if (curl_errno($ch) || empty($res)) {
            $strLog = <<<LOG
------------------------------
请求url：%s
curl_errno：%s
返回数据：%s
请求数据：%s
curl_error：%s
curl_getinfo：%s
------------------------------
LOG;
            $str = sprintf($strLog, $url, curl_errno($ch), json_encode($res), print_r($data, true), curl_error($ch), print_r(curl_getinfo($ch), true));
            file_put_contents('/data/logs/oa-kingnet/curl_error.log', $str . PHP_EOL, FILE_APPEND);
        }

        curl_close($ch);
        return $res;
    }
}

if (!function_exists('has_kingnet_app_access')) {
    /**
     * 是否为恺英app端访问
     * @return boolean [description]
     */
    function has_kingnet_app_access()
    {

        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/^KingnetOA/", $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        return false;
    }
}
if (!function_exists('p')) {
    function p($param, $flag = 0)
    {
        echo '<pre>';
        print_r($param);
        echo '</pre>';
        if ($flag) {
            die;
        }
    }
}

/**
 * 二维数组合并为一维数组
 */
if (!function_exists('merge_array')) {
    function merge_array($array)
    {
        return call_user_func_array('array_merge', $array);
    }
}

if (!function_exists('has_filter_wf_kingnet_app')) {
    /**
     * 是否为恺英通需要过滤的未上线流程
     * @param $type  需要过滤的类型，取值范围（1：流程类型p_type_id，2：流程p_id
     * @param $val   类型对应的值
     * @param $scope 判断范围，取值范围，true:限制移动端；flase:不限制
     * @return boolean [description]
     */
    function has_filter_wf_kingnet_app($type, $val, $scope = true)
    {

        if ($scope && !has_kingnet_app_access()) {
            return false;
        }

        $conf = "";
        switch ($type) {
            case 1:
                $conf = env('FILTER_WF_P_TYPE_ID');
                break;
            case 2:
                $conf = env('FILTER_WF_P_ID');
                break;
        }
        if (!empty($conf)) {
            $arr = explode(",", $conf);
            if (sizeof($arr)) {
                if (in_array($val, $arr)) {
                    return true;
                }
            }
        }
        return false;
    }
}

if (!function_exists('get_filter_wf_prefix')) {
    /**
     * 获取恺英通需要过滤的流程前缀
     * @return array
     */
    function get_filter_wf_prefix()
    {

        $res = [];
        $conf = env('FILTER_WF_PREFIX');
        if (!empty($conf)) {
            $res = explode(",", $conf);
        }
        return $res;
    }
}

if (!function_exists('get_filter_wf_pid')) {
    /**
     * 获取恺英通需要过滤的流程pid
     * @return array
     */
    function get_filter_wf_pid()
    {

        $res = [];
        $conf = env('FILTER_WF_P_ID');
        if (!empty($conf)) {
            $res = explode(",", $conf);
        }
        return $res;
    }
}
if (!function_exists('fields_validator')) {
    /**
     * @param $data 一维数组
     * @param $rules 验证规则
     * @param $messages 验证提示信息
     * @param int $type 1 返回单个错误， 其他返回所有错误
     * @return array|string
     */
    function fields_validator($data, $rules, $messages, $type = 1)
    {
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            $fail = $validator->messages()->toArray();

            foreach ($fail as $kf => $vf) {
                $fails[] = $vf[0];
                if ($type == 1) {
                    return $vf[0];
                }
            }
            return $fails;
        }
        return '';
    }
}

if (!function_exists('numeric_to_chinese')) {
    /**
     * 数字转换到中文数字
     * @param  [type] $num [description]
     * @return [type]      [description]
     */
    function numeric_to_chinese($num)
    {

        $capUnit = array('万', '亿', '万', '', '');
        $capDigit = array(2 => array('', '', ''), 4 => array('千', '百', '十', ''));
        $capNum = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
        if ((strpos(strval($num), '.') > 16) || (!is_numeric($num)))
            return '';

        $len = strlen($num);
        if ($len == 1) {
            return $capNum[$num];
        }
        $num = sprintf("%019.2f", $num);
        $CurChr = array('', '');
        for ($i = 0, $ret = '', $j = 0; $i < 5; $i++, $j = $i * 4 + floor($i / 4)) {
            $nodeNum = substr($num, $j, 4);
            for ($k = 0, $subret = '', $len = strlen($nodeNum); (($k < $len) && (intval(substr($nodeNum, $k)) != 0)); $k++) {
                $CurChr[$k % 2] = $capNum[$nodeNum{$k}] . (($nodeNum{$k} == 0) ? '' : $capDigit[$len][$k]);
                if (!(($CurChr[0] == $CurChr[1]) && ($CurChr[$k % 2] == $capNum[0])))
                    if (!(($CurChr[$k % 2] == $capNum[0]) && ($subret == '') && ($ret == '')))
                        $subret .= $CurChr[$k % 2];
            }
            $subChr = $subret . (($subret == '') ? '' : $capUnit[$i]);
            if (!(($subChr == $capNum[0]) && ($ret == '')))
                $ret .= $subChr;
        }
        $ret = ($ret == "") ? $capNum[0] . $capUnit[3] : $ret;
        if (strlen($ret) <= 9 && preg_match("/^一十/", $ret)) {
            return substr($ret, 3);
        }
        return $ret;
    }
}
if (!function_exists('check_file')) {
    function check_file($file, $arrFileType)
    {
        $arrName = explode(".", $file);
        if (count($arrName) != 2) {
            return '文件名格式错误';
        }
        if (!preg_match("/^\d+$/", $arrName[0])) {
            return '文件名错误';
        }
        $fileType = strtolower($arrName[1]);
        $nameSuffix = isset($arrFileType[$fileType]) ? $arrFileType[$fileType] : "";
        if (empty($nameSuffix)) {
            return '文件类型错误';
        }
        $filePath = sprintf("%s/%s.%s", env("UPLOAD_FILE_PATH"), $arrName[0], $nameSuffix);
        if (!file_exists($filePath)) {
            return '文件不存在';
        }
        return '';
    }
}

if (!function_exists('time_difference')) {
    function time_difference($time)
    {
        $day = floor($time / 86400);
        $hour = round($time % 86400 / 3600);
        $minute = ceil($time % 86400 / 60);

        if (empty($day) && empty($hour)) {
            $msg = $minute . '分钟';
        } elseif (empty($day) && !empty($hour)) {
            $msg = $hour . '小时';
        } elseif (!empty($day) && !empty($hour)) {
            $msg = $day . '天' . $hour . '小时';
        } elseif (!empty($day) && empty($hour)) {
            $msg = $day . '天';
        }
        return $msg;
    }
}

if (!function_exists('getMonthNum')) {
    function getMonthNum($date1, $date2, $tags = '-')
    {
        $date1 = explode($tags, $date1);
        $date2 = explode($tags, $date2);
        if ($date1[0] >= $date2[0]) {
            return abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);
        } else {
            return abs($date2[0] - $date1[0]) * 12 - abs($date1[1] - $date2[1]);
        }
    }
}

if (!function_exists('oa_post_header')) {
    /**
     * http post 请求
     * @param string $url 请求网址
     * @param string $data post数据
     */
    function oa_post_header($url, $data, $header)
    {
        $cheader[] = "Content-type: application/x-www-form-urlencoded";
        $cheader[] = "username: {$header}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //curl_setopt($ch, CURLOPT_HEADER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cheader);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERAGENT, "(kingnet oa web server)");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $res = curl_exec($ch);

        if (curl_errno($ch) || empty($res)) {
            $strLog = <<<LOG
------------------------------
请求url：%s
curl_errno：%s
返回数据：%s
请求数据：%s
curl_error：%s
curl_getinfo：%s
------------------------------
LOG;
            $str = sprintf($strLog, $url, curl_errno($ch), json_encode($res), print_r($data, true), curl_error($ch), print_r(curl_getinfo($ch), true));
            file_put_contents('/data/logs/oa-kingnet/curl_error.log', $str . PHP_EOL, FILE_APPEND);
        }

        curl_close($ch);
        return $res;
    }

}

if (!function_exists('oa_get_header')) {
    /**
     * http get 请求
     * @param string
     */
    function oa_get_header($url, $header)
    {
        $cheader[] = "Content-type: application/x-www-form-urlencoded";
        $cheader[] = "username: {$header}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cheader);
        curl_setopt($ch, CURLOPT_USERAGENT, "(kingnet oa web server)");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip()
    {
        $unknown = 'unknown';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        /*
        处理多层代理的情况
        或者使用正则方式：$ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
        */
        if (false !== strpos($ip, ',')) {
            $ip_arr = explode(',', $ip);
            $ip = reset($ip_arr);
        }

        return $ip;
    }
}

if (!function_exists('is_https')) {
    /**
     * 判断是否为https的
     */
    function is_https()
    {
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
            ? true : false;
    }
}

if (!function_exists('get_exception_message')) {
    /**
     * 获取异常信息
     *
     * @param $e
     * @return string
     */
    function get_exception_message($e)
    {
        $message = $e->getMessage();

        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            $message = '缺少token参数';
        } else if ($e instanceof \PDOException) {
            $message = '数据库错误';
        } else if (preg_match("/^[a-zA-Z-0-9]/", $e->getMessage())) {
            $message = '程序运行期错误';
        }

        return $message;
    }
}

/**
 * 数组 转 对象
 *
 * @param array $arr 数组
 * @return object
 */
function array_to_object($arr)
{
    if (gettype($arr) != 'array') {
        return;
    }
    foreach ($arr as $k => $v) {
        if (gettype($v) == 'array' || getType($v) == 'object') {
            $arr[$k] = (object)array_to_object($v);
        }
    }

    return (object)$arr;
}


/**
 * 对象 转 数组
 *
 * @param object $obj 对象
 * @return array
 */
function object_to_array($obj)
{
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }

    return $obj;
}


if (!function_exists('nl2br2')) {
    function nl2br2($string)
    {
        return str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
    }
}

if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '')
    {
        return "@$filename;filename="
            . ($postname ?: basename($filename))
            . ($mimetype ? ";type=$mimetype" : '');
    }
}

if (!function_exists('obs_object_url')) {
    function obs_object_url($objectName)
    {
        return config('obs.host') . '/' . config('obs.top_level_directory') . '/' . ltrim($objectName, '/');
    }
}

if (!function_exists('obs_object_url_prefix')) {
    function obs_object_url_prefix()
    {
        return config('obs.host') . '/' . config('obs.top_level_directory');
    }
}

if (!function_exists('obs_head_url_prefix')) {
    function obs_head_url_prefix()
    {
        return config('obs.host') . '/' . config('obs.head_directory');
    }
}

if (!function_exists('obs_curl_post')) {
    /**
     * @param string $url
     * @param array $data
     * @return mixed
     */
    function obs_curl_post($url, $data)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERAGENT, "(kingnet oa web server)");
        curl_setopt($ch, CURLOPT_TIMEOUT, 240);
        $res = curl_exec($ch);
        if (curl_errno($ch) || empty($res)) {
            $str = date('Y-m-d H:i:s') . ':' .
                $url . '--' .
                curl_errno($ch) . '--' .
                json_encode($res) . '--' .
                json_encode($data) . '--' .
                curl_error($ch) . '--' .
                json_encode(curl_getinfo($ch));
            file_put_contents('/data/logs/oa-kingnet/curl_error.log', $str . PHP_EOL, FILE_APPEND);
        }

        curl_close($ch);
        return $res;
    }
}

//下载文件 兼容本地 远程 地址
if (!function_exists('download_file')) {
    function download_file($localFilePath, $remoteFilePath, $downloadName)
    {
        set_time_limit(0);

        $header = get_headers($remoteFilePath, 1);

        if (empty($downloadName)) {
            $downloadName = !empty(basename($localFilePath)) ? basename($localFilePath) : (!empty(basename($remoteFilePath)) ? basename($remoteFilePath) : '');
        }

        if (file_exists($localFilePath)) {
            return response()->download($localFilePath, $downloadName);
        }

        $contentLength = isset($header['Content-Length']) ? $header['Content-Length'] : '';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        if (!empty($contentLength)) {
            header('Content-Length: ' . $contentLength);
        }
        $fp = fopen($remoteFilePath, 'rb');
        $chunkSize = 1024 * 1024;
        while (!feof($fp)) {
            $buffer = fread($fp, $chunkSize);
            echo $buffer;
        }
        fclose($fp);
        exit;
    }
}

if (!function_exists('get_host')) {
    function get_host()
    {
        $isHttps = isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1);
        $scheme = $isHttps ? 'https://' : 'http://';
        return $scheme . $_SERVER['HTTP_HOST'];
    }
}

/**
 * 变量友好化打印输出
 * @param variable $param 可变参数
 * @return void
 * @version php>=5.6
 * @example dump($a,$b,$c,$e,[.1]) 支持多变量，使用英文逗号符号分隔，默认方式 print_r，查看数据类型传入 .1
 */
function pp(...$param)
{

    $tag = is_cli() ? "\n" : "<pre>";
    echo $tag;
    if (end($param) === .1) {
        array_splice($param, -1, 1);

        foreach ($param as $k => $v) {
            echo $k > 0 ? $tag : '';

            ob_start();
            var_dump($v);

            echo preg_replace('/]=>\s+/', '] => <label>', ob_get_clean());
        }
    } else {
        foreach ($param as $k => $v) {
            echo $k > 0 ? $tag : '', print_r($v, true); // echo 逗号速度快 https://segmentfault.com/a/1190000004679782
        }
    }
    echo $tag;
}

/**
 * 变量友好化打印输出
 * @param variable $param 可变参数
 * @return void
 * @version php>=5.6
 * @example dump($a,$b,$c,$e,[.1]) 支持多变量，使用英文逗号符号分隔，默认方式 print_r，查看数据类型传入 .1
 */
function pd(...$param)
{

    $tag = is_cli() ? "\n" : "<pre>";
    echo $tag;
    if (end($param) === .1) {
        array_splice($param, -1, 1);

        foreach ($param as $k => $v) {
            echo $k > 0 ? $tag : '';

            ob_start();
            var_dump($v);

            echo preg_replace('/]=>\s+/', '] => <label>', ob_get_clean());
        }
    } else {
        foreach ($param as $k => $v) {
            echo $k > 0 ? $tag : '', print_r($v, true); // echo 逗号速度快 https://segmentfault.com/a/1190000004679782
        }
    }
    echo $tag;
    die;
}

if (!function_exists('get_page')) {
    /**
     * 数据分页
     * @param type $data 源数据
     * @param type $showNum 每页显示条数
     * @param type $page 第几页
     * @return type array
     */
    function get_page($data = [], $showNum = 10, $page = 1)
    {
        $pageData['total'] = count($data);
        $pageData['per_page'] = $showNum;
        $pageData['current_page'] = $page;
        $pageData['last_page'] = ceil(count($data) / $showNum);
        $pageData['data'] = array_slice($data, ($page - 1) * $showNum, $showNum);
        return $pageData;
    }
}

/*
* 判断日期格式是否正确
*/
function is_date($str, $format = 'Y-m-d')
{
    $unixTime_1 = strtotime($str);
    if (!is_numeric($unixTime_1)) return false; //如果不是数字格式，则直接返回
    $checkDate = date($format, $unixTime_1);
    $unixTime_2 = strtotime($checkDate);
    if ($unixTime_1 == $unixTime_2) {
        return true;
    } else {
        return false;
    }
}

if (!function_exists('xss_decode')) {
    /**
     * xss过滤
     * @param array $input 需要过滤的数组
     * @return array
     */
    function xss_decode($input)
    {
        if (is_object($input)) {
            foreach ($input as $k => $v) {
                if (is_array($v)) {
                    $input->$k = xss_decode($v);
                } else {
                    $input->$k = html_entity_decode($v, ENT_QUOTES, 'UTF-8');
                }
            }
            return $input;
        }
        if (is_array($input)) {
            if (sizeof($input)) {
                foreach ($input as $key => $value) {
                    if (is_array($value) && sizeof($value)) {
                        $input[$key] = xss_decode($value);
                    } else {
                        if (!empty($value)) {
                            if (is_string($value)) {
                                $input[$key] = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
                            } else {
                                $input[$key] = $value;
                            }
                        }
                    }
                }
            }
            return $input;
        }
        if (is_string($input)) {
            return html_entity_decode($input, ENT_QUOTES, 'UTF-8');
        } else {
            return $input;
        }
    }
}

if (!function_exists('gen_uid')) {
    function gen_uid()
    {
        do {
            $uid = str_replace('.', '0', uniqid(rand(0, 999999999), true));
        } while (strlen($uid) != 32);
        return $uid;
    }
}

if (!function_exists('time2second')) {
    function time2second($seconds)
    {
        $seconds = (int)$seconds;
        if ($seconds > 3600) {
            if ($seconds > 24 * 3600) {
                $days = (int)($seconds / 86400);
                $days_num = $days . "天";
                $seconds = $seconds % 86400;//取余

                $hours = intval($seconds / 3600);
                $minutes = $seconds % 3600;//取余下秒数
                $minutes = (int)gmstrftime("%M", $minutes);
                $time = $days_num . $hours . "时" . $minutes . "分";
            } else {
                $seconds = $seconds % 86400;//取余

                $hours = intval($seconds / 3600);
                $minutes = $seconds % 3600;//取余下秒数
                $minutes = (int)gmstrftime("%M", $minutes);
                $time = $hours . "时" . $minutes . "分";
            }

        } elseif ($seconds >= 60) {
            $minutes = (int)gmstrftime("%M", $seconds);
            $time = "{$minutes}分";
        } elseif ($seconds < 60) {
            // $time = "{$seconds}秒";
            $time = "1分";
        }
        return $time;
    }
}

if (!function_exists('is_cli')) {
    /*
    判断当前的运行环境是否是cli模式
    */
    function is_cli()
    {
        return preg_match("/cli/i", php_sapi_name()) ? true : false;
    }
}

/**
 * PHP大数组下，避免Mysql逐条执行，可以分批执行，提高代码效率
 */
if (!function_exists('insert_batch')) {
    function insert_batch($table, $keys, $values, $type = 'INSERT')
    {
        $tempArray = array();
        foreach ($values as $value) {
            $tempArray[] = implode('\', \'', $value);
        }
        return $type . ' INTO `' . $table . '` (`' . implode('`, `', $keys) . '`) VALUES (\'' . implode('\'), (\'', $tempArray) . '\')';
    }
}

/**
 * 流程可申请权限配置
 */
if (!function_exists('workflow_create_priv_type')) {
    function workflow_create_priv_type()
    {
        return [
            0 => '不能申请',
            1 => '全部',
            2 => '指定人',
            3 => '指定角色',
            4 => '指定部门',
            5 => '辞退流程申请人',
            6 => '全职员工',
        ];
    }
}

/**
 * 流程创建类型配置
 */
if (!function_exists('workflow_create_type')) {
    function workflow_create_type()
    {
        return [
            1 => 'OA申请',
            2 => '第三方接口申请',
            3 => '都支持',
        ];
    }
}

if (!function_exists('is_email')) {
    function is_email($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }
}