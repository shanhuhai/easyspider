<?php
define('EP_ROOT_PATH', __DIR__. '/');
define('EP_LIB_PATH', EP_ROOT_PATH.'lib/');
define('EP_CACHE_PATH', EP_ROOT_PATH. 'cache/');
require EP_LIB_PATH.'functions.php';

if(!defined('EP_TASK_NAME')){
    return ;
}
set_time_limit(0);
set_include_path(get_include_path().
    PATH_SEPARATOR. EP_ROOT_PATH . 'lib'.
    PATH_SEPARATOR . EP_ROOT_PATH. 'tasks'. DIRECTORY_SEPARATOR . EP_TASK_NAME .
    DIRECTORY_SEPARATOR.'lib');


spl_autoload_register(function($className){
    $classNameInfo = explode('_', $className);
    if(count($classNameInfo) > 1) {
        $className = str_replace('_', '/', $className);
    }
    $className = strtolower($className);
    $file = $className . ".php";
    require_once $file;
});
