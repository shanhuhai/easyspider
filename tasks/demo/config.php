<?php


define('EP_DEBUG_MODE', true);
//define('EP_DEBUG_MODE', false);

define('EP_UPDATE_MODE', false);
//define('EP_UPDATE_MODE', true);

define('EP_LOAD_THUMB', true);
//define('EP_LOAD_THUMB', false);

define('EP_TASK_NAME', 'demo');
require_once('/Users/shanhuhai/wwwroot/easyspider/easyspider.php');

define('EP_FILE_PATH', EP_ROOT_PATH.'file/demo/');
define('EP_FILE_DOMAIN', 'http://upload.jquerycn.cn/');

$settings = array(
    'domain'=>'http://www.jb51.net',
    'anyPageUrl'=>'',
    'charset'=>'gbk',
    'cacheList'=>1,
    'cachePage'=>1,
    'reverseList'=>0,
    'dbConfig'=> array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'username' => 'root',
        'password' =>'root12',
        'dbname' => 'test',
        'pconnect' => 0,
        'charset' => 'utf8'
    )
);

$listConfigs = array (
  0 => 
  array (
    'catid' => '1',
    'urlReg' => '/list/list_15_{d}.htm',
    'firstUrl' => '/list/list_15_1.htm',
    'start' => '1',
    'end' => '28',
    'updateEnd' => '3',
    'parseCss' => '',
    'listType' => 'html',
    'dislocation' => 0,
    'domain' => '',
    'fields' => 
    array (
      'content' => '',
      'tags' => '',
    ),
  ),
);

