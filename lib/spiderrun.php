<?php
class SpiderRun{
    protected $taskConfig;

    static private $listIns;
    static private $pageIns;

    static private $listConfigs;

    static public function getLists(array $listConfigs){
        self::$listConfigs = $listConfigs;
        self::$listIns = new TaskList();
        foreach(self::$listConfigs as $config){
            self::$listIns->setConfig($config);
            self::$listIns->getList();
        }
        echo "lists done.\n";
    }

    static public function readLists(array $listConfigs){
        self::$listConfigs = $listConfigs;
        self::$pageIns = new TaskPage();
        $processFilePath = EP_CACHE_PATH.'taskdata/'.EP_TASK_NAME.'/';
        $processFile = 'aa.process.php';
        $processData = readCache($processFile, $processFilePath);
     //   var_dump($processData);exit;
        $index = 0;
        if(!empty($processData)){
            $index = $processData['index'];
        } else {
            $processData = array(
                'index'=>0,
                'catid'=>self::$listConfigs[0]['catid'],
                'current'=>0,
                'url'=>'',
                'filename'=>''
            );
            writeCache($processFile, $processData, $processFilePath);
        }
        foreach(self::$listConfigs as $key=> $config){
            if($key < $index){
                continue;
            }

            self::$pageIns->setConfig($config);
            self::$pageIns->readList();
            $processData['index'] = $key+1;
            $processData['catid'] = self::$listConfigs[$key+1]['catid'];
            $processData['current'] = 0;
            $processData['url'] = '';
            $processData['filename'] = '';
            writeCache($processFile, $processData, $processFilePath);
        }
        echo "pages done\n";
    }
}
