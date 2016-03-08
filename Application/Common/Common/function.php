<?php

/* redis的初始化 */

function get_redis() {
    $redis = new Redis();
    $redis->connect("localhost", 6379);
    $redis->auth('liuyongtao');
    return $redis;
}

//距现在多少月,天,小时,分钟,秒前
function time_tranx($the_time) {
    $now_time = date("Y-m-d H:i:s", time());
    $now_time = strtotime($now_time);
    $show_time = strtotime($the_time);
    $dur = $now_time - $show_time;
    if ($dur < 0) {
        return $the_time;
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 86400 * 7) {
//3天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        if ($dur < 86400 * 30) {
                            return floor($dur / (86400 * 7)) . '周前';
                        } else {
                            return floor($dur / (86400 * 30)) . '月前';
                        }
                    }
                }
            }
        }
    }
}

/**
 * xml文件转化为数组
 * @param unknown $url
 * @param number $get_attributes
 * @param string $priority
 * @return void|multitype:
 */
function xml2array($url, $get_attributes = 1, $priority = 'tag') {
    $contents = "";
    if (!function_exists('xml_parser_create')) {
        return array();
    }
    $parser = xml_parser_create('');
    if (!($fp = @ fopen($url, 'rb'))) {
        return array();
    }
    while (!feof($fp)) {
        $contents .= fread($fp, 8192);
    }
    fclose($fp);
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);
    if (!$xml_values)
        return; //Hmm...
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();
    $current = & $xml_array;
    $repeated_tag_index = array();
    foreach ($xml_values as $data) {
        unset($attributes, $value);
        extract($data);
        $result = array();
        $attributes_data = array();
        if (isset($value)) {
            if ($priority == 'tag')
                $result = $value;
            else
                $result['value'] = $value;
        }
        if (isset($attributes) and $get_attributes) {
            foreach ($attributes as $attr => $val) {
                if ($priority == 'tag')
                    $attributes_data[$attr] = $val;
                else
                    $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }
        if ($type == "open") {
            $parent[$level - 1] = & $current;
            if (!is_array($current) or ( !in_array($tag, array_keys($current)))) {
                $current[$tag] = $result;
                if ($attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                $current = & $current[$tag];
            }
            else {
                if (isset($current[$tag][0])) {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    $repeated_tag_index[$tag . '_' . $level] ++;
                } else {
                    $current[$tag] = array(
                        $current[$tag],
                        $result
                    );
                    $repeated_tag_index[$tag . '_' . $level] = 2;
                    if (isset($current[$tag . '_attr'])) {
                        $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                        unset($current[$tag . '_attr']);
                    }
                }
                $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                $current = & $current[$tag][$last_item_index];
            }
        } elseif ($type == "complete") {
            if (!isset($current[$tag])) {
                $current[$tag] = $result;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                if ($priority == 'tag' and $attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
            }
            else {
                if (isset($current[$tag][0]) and is_array($current[$tag])) {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    if ($priority == 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag . '_' . $level] ++;
                } else {
                    $current[$tag] = array(
                        $current[$tag],
                        $result
                    );
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $get_attributes) {
                        if (isset($current[$tag . '_attr'])) {
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                        if ($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag . '_' . $level] ++; //0 and 1 index is already taken
                }
            }
        } elseif ($type == 'close') {
            $current = & $parent[$level - 1];
        }
    }
    return ($xml_array);
}

/**
 * 数组排序
 * @param unknown $arr
 * @param unknown $keys
 * @param string $type
 * @return multitype:unknown
 */
function array_sort($arr, $keys, $type = 'asc') {
    $keysvalue = $new_array = array();
    foreach ($arr as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

/**
 * 模拟发送，post模式
 * @param unknown $remote_server
 * @param unknown $post_string
 * @return mixed
 */
function request_by_curl($remote_server, $post_string) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_server);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "auto post");
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/*
 * curl封装函数
 * 
 */

function curls($url, $timeout = '10') {
    // 1. 初始化
    $ch = curl_init();
    // 2. 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // 3. 执行并获取HTML文档内容
    $info = curl_exec($ch);
    // 4. 释放curl句柄
    curl_close($ch);

    return $info;
}

/*
 * curl通过POST方式发送数据
 * 
 */

function curl_post($url, $post) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $return = curl_exec($ch);
    curl_close($ch);
    return $return;
}

// 测试写入文件
function testwrite($d) {
    $tfile = 'cms.txt';
    $d = ereg_replace('/$', '', $d);
    $fp = @fopen($d . '/' . $tfile, 'w');
    if (!$fp) {
        return false;
    } else {
        fclose($fp);
        $rs = @unlink($d . '/' . $tfile);
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }
}

// 获取文件夹大小
function getdirsize($dir) {
    $dirlist = opendir($dir);
    while (false !== ($folderorfile = readdir($dirlist))) {
        if ($folderorfile != "." && $folderorfile != "..") {
            if (is_dir("$dir/$folderorfile")) {
                $dirsize += getdirsize("$dir/$folderorfile");
            } else {
                $dirsize += filesize("$dir/$folderorfile");
            }
        }
    }
    closedir($dirlist);
    return $dirsize;
}

function getrexie($str) {
    return str_replace('@@@', '', str_replace('/@@@', '', $str . '@@@'));
}

function getaddxie($str) {
    return str_replace('@@@', '', str_replace('//@@@', '/', $str . '/@@@'));
}

// 数组保存到文件
function arr2file($filename, $arr = '') {
    if (is_array($arr)) {
        $con = var_export($arr, true);
    } else {
        $con = $arr;
    }
    $con = "<?php\nreturn $con;\n?>"; //\n!defined('IN_MP') && die();\nreturn $con;\n
    write_file($filename, $con);
}

function mkdirss($dirs, $mode = 0777) {
    if (!is_dir($dirs)) {
        mkdirss(dirname($dirs), $mode);
        return @mkdir($dirs, $mode);
    }
    return true;
}

function write_file($l1, $l2 = '') {
    $dir = dirname($l1);
    if (!is_dir($dir)) {
        mkdirss($dir);
    }
    return @file_put_contents($l1, $l2);
}

function read_file($l1) {
    return @file_get_contents($l1);
}

// 转换成JS
function t2js($l1, $l2 = 1) {
    $I1 = str_replace(array("\r", "\n"), array('', '\n'), addslashes($l1));
    return $l2 ? "document.write(\"$I1\");" : $I1;
}

//utf8转gbk
function u2g($str) {
    return iconv("UTF-8", "GBK", $str);
}

//gbk转utf8
function g2u($str) {
    return iconv("GBK", "UTF-8//ignore", $str);
}

//获取当前地址栏URL
function http_url() {
    return htmlspecialchars("http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]);
}

//获得某天前的最后一秒时间戳
function xtime($day) {
    $day = intval($day);
    return mktime(23, 59, 59, date("m"), date("d") - $day, date("y"));
}

// 获取相对目录
function get_base_path($filename) {
    $base_path = $_SERVER['PHP_SELF'];
    $base_path = substr($base_path, 0, strpos($base_path, $filename));
    return $base_path;
}

// 获取相对路径
function get_base_url($baseurl, $url) {
    if ("#" == $url) {
        return "";
    } elseif (FALSE !== stristr($url, "http://")) {
        return $url;
    } elseif ("/" == substr($url, 0, 1)) {
        $tmp = parse_url($baseurl);
        return $tmp["scheme"] . "://" . $tmp["host"] . $url;
    } else {
        $tmp = pathinfo($baseurl);
        return $tmp["dirname"] . "/" . $url;
    }
}

//输入过滤 同时去除连续空白字符可参考扩展库的remove_xss
function get_replace_input($str, $rptype = 0) {
    $str = stripslashes($str);
    $str = htmlspecialchars($str);
    $str = get_replace_nb($str);
    return addslashes($str);
}

//去除换行
function get_replace_nr($str) {
    $str = str_replace(array("<nr/>", "<rr/>"), array("\n", "\r"), $str);
    return trim($str);
}

//去除连续空格
function get_replace_nb($str) {
    $str = str_replace("&nbsp;", ' ', $str);
    $str = str_replace("　", ' ', $str);
    $str = ereg_replace("[\r\n\t ]{1,}", ' ', $str);
    return trim($str);
}

//去除所有标准的HTML代码
function get_replace_html($str, $start = 0, $length, $charset = "utf-8", $suffix = false) {
    return msubstr(eregi_replace('<[^>]+>', '', ereg_replace("[\r\n\t ]{1,}", ' ', get_replace_nb($str))), $start, $length, $charset, $suffix);
}

//判断是否属于当前模块
function check_model($modelname) {
    if (strtolower(MODULE_NAME) == $modelname) {
        return 1;
    }
    return 0;
}

// 获取广告调用地址
function get_cms_ads($str, $charset = "utf-8") {
    return '<script type="text/javascript" src="' . C('web_path') . C('web_adsensepath') . '/' . $str . '.js" charset="utf-8"></script>';
}

// 获取标题颜色
function get_color_title($str, $color) {
    if (empty($color)) {
        return $str;
    } else {
        return '<font color="' . $color . '">' . $str . '</font>';
    }
}

// 获取时间颜色
function get_color_date($type = 'Y-m-d H:i:s', $time, $color = 'red') {
    if ($time > xtime(1)) {
        return '<font color="' . $color . '">' . date($type, $time) . '</font>';
    } else {
        return date($type, $time);
    }
}

//生成字母前缀
function get_letter($s0) {
    $firstchar_ord = ord(strtoupper($s0{0}));
    if (($firstchar_ord >= 65 and $firstchar_ord <= 91)or ( $firstchar_ord >= 48 and $firstchar_ord <= 57))
        return $s0{0};
    $s = iconv("UTF-8", "gb2312", $s0);
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 and $asc <= -20284)
        return "A";
    if ($asc >= -20283 and $asc <= -19776)
        return "B";
    if ($asc >= -19775 and $asc <= -19219)
        return "C";
    if ($asc >= -19218 and $asc <= -18711)
        return "D";
    if ($asc >= -18710 and $asc <= -18527)
        return "E";
    if ($asc >= -18526 and $asc <= -18240)
        return "F";
    if ($asc >= -18239 and $asc <= -17923)
        return "G";
    if ($asc >= -17922 and $asc <= -17418)
        return "H";
    if ($asc >= -17417 and $asc <= -16475)
        return "J";
    if ($asc >= -16474 and $asc <= -16213)
        return "K";
    if ($asc >= -16212 and $asc <= -15641)
        return "L";
    if ($asc >= -15640 and $asc <= -15166)
        return "M";
    if ($asc >= -15165 and $asc <= -14923)
        return "N";
    if ($asc >= -14922 and $asc <= -14915)
        return "O";
    if ($asc >= -14914 and $asc <= -14631)
        return "P";
    if ($asc >= -14630 and $asc <= -14150)
        return "Q";
    if ($asc >= -14149 and $asc <= -14091)
        return "R";
    if ($asc >= -14090 and $asc <= -13319)
        return "S";
    if ($asc >= -13318 and $asc <= -12839)
        return "T";
    if ($asc >= -12838 and $asc <= -12557)
        return "W";
    if ($asc >= -12556 and $asc <= -11848)
        return "X";
    if ($asc >= -11847 and $asc <= -11056)
        return "Y";
    if ($asc >= -11055 and $asc <= -10247)
        return "Z";
    return 0;
}

//excel转换为数组
function excelToArray($file) {

    $objReader = PHPExcel_IOFactory::createReader('Excel5');

    $objReader->setReadDataOnly(true);

    $objPHPExcel = $objReader->load($file);

    $objWorksheet = $objPHPExcel->getActiveSheet();

    $highestRow = $objWorksheet->getHighestRow();

    $highestColumn = $objWorksheet->getHighestColumn();

    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

    $excelData = array();

    for ($row = 2; $row <= $highestRow; ++$row) {

        for ($col = 0; $col <= $highestColumnIndex; ++$col) {

            $excelData[$row][] = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
        }
    }

    return $excelData;
    // var_dump($excelData); 
}

/**
 * 邮件发送函数
 */
function sendMail($to, $title, $content) {

    import("PHPMailer", "Core/Lib/Widget/PHPMailer", ".class.php");
    $mail = new PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host = C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
    $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
    $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAIL_PASSWORD'); //邮箱密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->AddAddress($to, "尊敬的客户");
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
    $mail->CharSet = C('MAIL_CHARSET'); //设置邮件编码
    $mail->Subject = $title; //邮件主题
    $mail->Body = $content; //邮件内容
    $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
    return($mail->Send());
}
//获取访问终端是PC还是移动设备
function is_mobile() {
	$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
	$mobile_browser = '0';

	if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		$mobile_browser++;
	}

	if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) {
		$mobile_browser++;
	}

	if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
		$mobile_browser++;
	}

	if (isset($_SERVER['HTTP_PROFILE'])) {
		$mobile_browser++;
	}

	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
	$mobile_agents = array(
		'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
		'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
		'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
		'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
		'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
		'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
		'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
		'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
		'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-',
	);
	if (in_array($mobile_ua, $mobile_agents)) {
		$mobile_browser++;
	}

	if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) {
		$mobile_browser++;
	}

	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) {
		$mobile_browser = 0;
	}

	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) {
		$mobile_browser++;
	}

	if ($mobile_browser) {
		return true;
	} else {
		return false;
	}
}
