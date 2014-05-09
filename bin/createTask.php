<?php

require '../easyspider.php';
require '../lib/Spyc.php';
require EP_LIB_PATH.'view.php';

$configFile = $argv[1];
if(!file_exists($configFile)){
    echo "$configFile is not exist.\n";
    exit;
}

$taskConfig = Spyc::YAMLLoad($configFile);
$taskName = $taskConfig['taskName'];
$domain = $taskConfig['domain'];
$charset = $taskConfig['charset'];
$fileDomain = $taskConfig['fileDomain'];
$listNum = count($taskConfig['lists']);
$parseCss = $taskConfig['parseCss'];
$listType = $taskConfig['listType'];
$updateEnd = $taskConfig['updateEnd'];
$dislocation = $taskConfig['dislocation'];
$fields = explode(',', $taskConfig['fields']);

$taskDir = EP_ROOT_PATH.'tasks/'.$taskName;
if(isset($argv[2]) && $argv[2] == '-f'){
    deldir($taskDir);
}
if(is_dir($taskDir)){
    echo "task exists\n";
    exit;
}


@mkdir(EP_ROOT_PATH.'tasks/'.$taskName, 0775);
!is_dir(EP_ROOT_PATH.'file/') && @mkdir(EP_ROOT_PATH.'file/', 0755);
@mkdir(EP_ROOT_PATH.'tasks/'.$taskName.'/lib', 0755);

$view = View::getInstance(array(
    'ext'=>'.tpl',
    'dir'=>EP_ROOT_PATH.'bin/tpl/'
));

$callbackFuncStr = '';


foreach($fields as $f){
    if(in_array($f,array('thumb','source', 'catid', 'content' , 'from', 'created'))){
        continue;
    }

    $callbackFuncStr .= '
    protected function get_'.$f.'($dom, $data, $content, $nowUrl){
'.$view->fetch('page/default').'
    }
    ';
}

$callbackFuncStr .= $view->fetch('page/content');
$callbackFuncStr .= $view->fetch('page/checkExists');
$callbackFuncStr .= $view->fetch('page/saveData');


$view->cleanVars();
$view->assign('callbackFuncStr', $callbackFuncStr);
$content = $view->fetch('taskPage');
$content = "<?php \n". $content;
file_put_contents( EP_ROOT_PATH.'tasks/'.$taskName.'/lib/taskpage.php', $content);
copy('./tpl/taskList.tpl', EP_ROOT_PATH.'tasks/'.$taskName.'/lib/tasklist.php');

$view->cleanVars();
$view->assign($taskConfig);
$tmp = array();
foreach($fields as $f){
    $tmp[$f] = '';
}
$fields = $tmp;
$listConfigs = array();
foreach($taskConfig['lists'] as $c){
    list($urlReg, $firstUrl, $end, $originCatename, $catname, $catid) = explode('```', $c);
    $listConfigs[] = array(
        'catid'=>$catid,
        'urlReg'=>$urlReg,
        'firstUrl'=>$firstUrl,
        'start'=>'1',
        'end'=>$end,
        'updateEnd'=>$updateEnd,
        'parseCss'=>$parseCss,
        'listType'=>$listType,
        'dislocation'=> $dislocation ,
        'domain'=>'',
        'fields'=>$fields,
    );
}

$view->assign('listConfigs', var_export($listConfigs, true));
$view->assign('rootPath', EP_ROOT_PATH);
$content = $view->fetch('config');
$content = "<?php\n".$content;
file_put_contents(EP_ROOT_PATH.'tasks/'.$taskName.'/config.php', $content);

copy('./tpl/fetchList.tpl', EP_ROOT_PATH.'tasks/'.$taskName.'/fetchList.php');
copy('./tpl/fetchPage.tpl', EP_ROOT_PATH.'tasks/'.$taskName.'/fetchPage.php');
copy('./tpl/update.tpl', EP_ROOT_PATH.'tasks/'.$taskName.'/update.php');
touch(EP_ROOT_PATH.'tasks/'.$taskName.'/publish.php');




?>