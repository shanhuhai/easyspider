<?php
require('../../easyspider.php');
$originFile = $argv[1];
if(!file_exists($originFile)){
    echo "$originFile is not exist.\n";
    exit;
}

$lines = file($originFile);
$yaml = "---\n";
$i = 0;
$busiName = '';
$list = array();
$taskName = '';
$firstUrl = '';
$urlReg = '';
$url = '';
foreach($lines as &$l){
    $l = trim($l);
    if(empty($l)){
        continue;
    }

    if($i<1){
        list($taskName,$firstUrl, $urlReg) = explode('|',$l);

        $str = "";
        $yaml .= $str."categorys:\n";
    } else {
        $yaml .= '- '.trim($l)."\n";
        $list[] = "    - {$firstUrl}```{$urlReg}```0```{$l}";
    }
    $i++;
}
$yaml .= '...';
$taskNameAlias = pinyin($taskName);
file_put_contents(EP_ROOT_PATH.'bin/conf/'.$taskNameAlias.'.conf', $yaml);

$siteStr = '
taskName: '.$taskName.' #任务名称
domain: http://www.cmiyu.com
charset: utf-8
fileDomain: http://file.975k.com/ #远程文件本地话后本地使用的域
lists:
'.implode("\n", $list).'
parseCss: ""
listType: "html"
updateEnd: "3"
dislocation: 0
fields: "descp,content"
';
file_put_contents(EP_ROOT_PATH.'bin/conf/'.$taskName.'.conf', $siteStr);
