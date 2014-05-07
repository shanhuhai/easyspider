<?php
function str_charset($in_charset, $out_charset, $str_or_arr){
    $lang = array(&$in_charset, &$out_charset);
    foreach ($lang as &$l){
        switch (strtolower(substr($l, 0, 2))){
            case 'gb': $l = 'gbk';
                break;
            case 'bi': $l = 'big5';
                break;
            case 'ut': $l = 'utf-8';
                break;
        }
    }
    if(is_array($str_or_arr)){
        foreach($str_or_arr as &$v){
            $v = str_charset($in_charset, $out_charset.'//IGNORE', $v);
        }
    } else {
        $str_or_arr = iconv($in_charset, $out_charset.'//IGNORE', $str_or_arr);
    }
    return $str_or_arr;
}

function pinyin($str, $charset = "utf-8", $ishead = 0){
    $restr = '';
    $str = trim($str);

    if ($charset == "utf-8") {
        $str = iconv("utf-8", "gb2312", $str);
    }

    $slen = strlen($str);

    $pinyins = array();
    if ($slen < 2) {
        return $str;
    }
    $fp = fopen(EP_ROOT_PATH.'lib/pinyin.dat', 'r');

    if(false == $fp) {
        exit('pinyin.dat open failed');
    }
    while (!feof($fp)) {
        $line = trim(fgets($fp));
        $pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
    }
    fclose($fp);

    for ($i = 0; $i < $slen; $i++) {
        if (ord($str[$i]) > 0x80) {
            $c = $str[$i] . $str[$i + 1];
            $i++;
            if (isset($pinyins[$c])) {
                if ($ishead == 0) {
                    $restr .= $pinyins[$c];
                } else {
                    $restr .= $pinyins[$c][0];
                }
            } else {
                $restr .= "_";
            }
        } else if (preg_match("/[a-z0-9]/i", $str[$i])) {
            $restr .= $str[$i];
        } else {
            $restr .= "_";
        }
    }
    return $restr;
}

function returnJson($data){
    header('Content-type:text/html;Charset=utf-8');
    echo isset($_GET['callback']) ? $_GET['callback'] . '(' . json_encode($data) . ')' : json_encode($data);
    exit;
}

function connectMysql($config){
    $link = mysql_connect($config['host'], $config['username'], $config['password'])
    or die('Could not connect: ' . mysql_error());
    mysql_query("set names utf8", $link);
    mysql_select_db($config['dbname'])
    or die('Could not select database'."\n");
    return $link;
}

function curl_get_content($url, $refer = ''){
    $ch = curl_init();
    $options = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_URL => $url,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_REFERER => $refer,
        CURLOPT_USERAGENT => "Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)"
    );
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

function writeFile($file, $data){
    $dir = dirname($file);
    if(!is_dir($dir)){
        mkdir($dir, 0755, true);
    }
    $result = @file_put_contents($file, $data);
    $result && chmod($file, 0755);
    return $result;
}

function writeCache($file, $array, $path = null){
    if(!is_array($array)) return false;
    $array = "<?php\nreturn ".var_export($array, true).";";
    $cachefile = ($path ? $path : EP_CACHE_PATH).$file;
    $strlen = writeFile($cachefile, $array);
    return $strlen;
}

function readCache($file, $path = null){
    if(!$path) $path = EP_CACHE_PATH;
    $cachefile = $path.$file;
    return @include $cachefile;
}

function config($configName, $key = null, $default = null) {
    $config = require_once(EP_ROOT_PATH.'/config/'.$configName.'.php');
    return is_null($key) ? $config : (isset($config[$key]) ? $config[$key] : $default);
}

function url2fileName($url){
    $urlinfo =parse_url( $url);
    $urlinfo['path'] = str_replace(array('/','?'), array('_','_'), $urlinfo['path']);
    return implode('_', $urlinfo);
}


/*去除tag 以及内部的内容*/
function stripTagsFull($text, $tags = array()){
    $args = func_get_args();
    $text = array_shift($args);
    $tags = func_num_args() > 2 ? array_diff($args,array($text))  : (array)$tags;
    foreach ($tags as $tag){
        if(preg_match_all('/<'.$tag.'[^>]*>(.*)<\/'.$tag.'>/is', $text, $found)){
            $text = str_replace($found[0],'',$text);
        }
    }
    return $text;
}


function saveRemoteFile($sourceUrl, $originDomain = '', $rootPath = '', $localDomain = ''){
    if($rootPath == '') {
        $rootPath = EP_FILE_PATH;
    }

    if($localDomain == '') {
        $localDomain = EP_FILE_DOMAIN;
    }

    if(strpos($sourceUrl,'://') === false){
        $sourceUrl = $originDomain.$sourceUrl;
    }
    $fileName = getFileNameFromUrl($sourceUrl);
    //根据文件名生成子目录
    $md5 = md5($fileName);
    $path =  substr($md5,0,1) . '/'.substr($md5,1,1).'/';

    $filePath = $rootPath.$path;
    !is_dir($filePath) && mkdir($filePath, 0775, true);

    //根据旧文件名生成新文件名
    $t = array();
    $t = explode('.', $fileName);
    $newFileName = time() . '_' . rand(1000, 9999) . '.' . strtolower($t[count($t) - 1]);

    //确定文件的绝对路径
    $fileLocation = $rootPath.$path.$newFileName;
    //确定文件的本地url
    $localUrl = $localDomain.$path . $newFileName;
    $result = copy($sourceUrl, $fileLocation);
    if($result){
        return $localUrl;
    } else {
        return false;
    }
}

function saveContentPic($content, $originDomain){
    $allowExts = 'png|jpg|gif|jpeg|bmp';
    return preg_replace('/((http:\/\/)?[^>\'"]+\.('.$allowExts.'))/ie', "saveRemoteFile('\\1','$originDomain')", $content);
}

function fetchUrl($url, $file, $charset = 'utf-8', $refer = '', $cache = true){

    if (file_exists($file) && $cache) {
        $content = file_get_contents($file);
    } else {
        $content = curl_get_content($url, $refer);
        if ($charset != 'utf-8') {
            $content = str_charset($charset, 'utf-8', $content);
        }
        if($cache){
            writeFile($file, $content);
        }
    }
    return $content;
}


function logInfo($msg, $type = 'INFO'){
    $logFile = defined('EP_TASK_NAME') ? EP_CACHE_PATH. 'tasklog/'. EP_TASK_NAME. '.log' : EP_ROOT_PATH. 'cache/error.log';
    $content = '';
    if(file_exists($logFile)){
        $content = file_get_contents($logFile);
    }
    $msg ='['.$type.']'. date("Y-m-d H:i:s"). ": ". $msg. "\r\n";
    echo $msg;
    $msg = $content. $msg;
    writeFile($logFile, $msg);
}

/*
 *类似于needle，取needle后面的字符串
 * @params string $haystack
 * @params string $needle
 * @return string
 */
function strstrb($haystack, $needle){
    return substr($haystack, strpos($haystack, $needle) + strlen($needle));
}

function getFileNameFromUrl($url){
    $pathInfo = pathinfo($url);
    return $pathInfo['basename'];
}

function getHostFromUrl($url){
    $tmp = parse_url($url);
    return $tmp['host'];
}

function deldir($dir) {
    $dh=opendir($dir);
    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }
    closedir($dh);
    if(rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}