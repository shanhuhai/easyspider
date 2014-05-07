<?php
abstract class SpiderPage {
    protected $taskConfig;

    protected $catid;
    protected $fields;

    public function setConfig(array $config = array()){
        $this->catid = $config['catid'];
        $this->fields = $config['fields'];
        $this->domain = isset($config['domain']) && !empty($config['domain'])
            ? $config['domain'] : $this->taskConfig['domain'];
    }

    protected function checkExists($dom, $con){
        return false;
    }

    protected function processUrl($url){
        return $url;
    }

    abstract protected function saveData($data, $dom);

    public function readList(){
        $processFile = 'aa.process.php';
        $processFilePath = EP_CACHE_PATH.'taskdata/'.EP_TASK_NAME.'/';
        $processData = readCache($processFile, $processFilePath);

        $current = $processData['current'];

        $listName = 'list_' . $this->catid . '.php';
        $urls = readCache($listName,$processFilePath);

        $end = count($urls)-1;

        while($current<=$end) {
            $tmp = explode('|', $urls[$current]);
            $nowUrl = $tmp[0];
            $thumb_source = $tmp[1];
            $title = $tmp[2];
            if(strpos($nowUrl, 'http://') === false) {
                $nowUrl =  $this->domain.$nowUrl;
            }

            $nowUrl = $this->processUrl($nowUrl);

            $fileName = 'con_' . url2fileName( $nowUrl ) . '.html';

            $processData['current'] = $current;
            $processData['url'] = $nowUrl;
            $processData['filename'] = $fileName;

            writeCache($processFile, $processData, $processFilePath);

            $content = fetchUrl($nowUrl, EP_ROOT_PATH.'cache/html/'.EP_TASK_NAME.'/'.$fileName,
            $this->taskConfig['charset'], $this->taskConfig['domain'],
            $this->taskConfig['cachePage']);

            require_once(EP_LIB_PATH.'simple_html_dom.php');
            $dom = str_get_html($content);

            $data = array();

            $exist = $this->checkExists($title, $dom, $content, $nowUrl);
            if($exist){
                $current++;
                continue;
            }
            foreach($this->fields as $key => $value){
                if(in_array($key,
                    array('title', 'thumb', 'thumb_source', 'source', 'catid', 'created'))){
                    continue;
                }
                $callBack = 'get_'.$key;
                $data[$key] = !empty($value)
                    ? $value
                    : $this->$callBack($dom,  &$data, $content, $nowUrl);
            }

            $thumb = '';
            if(!empty($thumb_source) && EP_LOAD_THUMB){
                $thumb = saveRemoteFile($thumb_source);
            }
            $data['title'] = $title;
            $data['thumb'] = $thumb;
            $data['thumb_source'] = $thumb_source;
            $data['source'] = $nowUrl;
            $data['catid'] = $this->catid;
            $data['created'] = time();
            $this->saveData($data , $dom);
            if(EP_DEBUG_MODE){
                echo "data:\n";
                var_dump($data);
                $error = mysql_error();
                if(!empty($error)){
                    echo "insert error:\n";
                    echo mysql_error()."\n";
                }
                echo "page debug mode.\n";
                exit;
            }
            $dom->__destruct();
            $current++;
        }
        echo("page " .$this->catid ." done.\n");

    }
}
