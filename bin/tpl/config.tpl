

define('EP_DEBUG_MODE', true);
//define('EP_DEBUG_MODE', false);

define('EP_UPDATE_MODE', false);
//define('EP_UPDATE_MODE', true);

define('EP_LOAD_THUMB', true);
//define('EP_LOAD_THUMB', false);

define('EP_TASK_NAME', '<?php echo $taskName ?>');
require_once('<?php echo $rootPath ?>easyspider.php');

define('EP_FILE_PATH', EP_ROOT_PATH.'file/<?php echo $taskName?>/');
define('EP_FILE_DOMAIN', '<?php echo $fileDomain ?>/');



$settings = array(
    'domain'=>'<?php echo $domain ?>',
    'anyPageUrl'=>'',
    'charset'=>'<?php echo $charset ?>',
    'cacheList'=>1,
    'cachePage'=>1,
    'reverseList'=>0,
    'dbConfig'=> array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'username' => 'root',
        'password' =>'***EDITME***',
        'dbname' => '***EDITME***',
        'pconnect' => 0,
        'charset' => 'utf8'
    )
);

$listConfigs = <?php echo $listConfigs ?>;

