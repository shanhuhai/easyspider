<?php
abstract class SpiderList{
    protected $taskConfig;

    private $catid;
    private $urlReg;
    private $firstUrl;
    private $start;
    private $end;
    private $updateEnd;
    private $listType;
    private $parseCss; //解析列表用的 css 语法
    private $dislocation; // 分页序号与实际页错开的值
    private $domain; //当各个列表的域各不相同时使用

    /*
     *
     */
    public function __construct( array $config = array() ){
        if(!empty($config)){
            $this->setConfig($config);
        }
    }

    public function setConfig(array $config = array()){
        $this->catid = $config['catid'];
        $this->urlReg = $config['urlReg'];
        $this->start = $config['start'];
        $this->end = $config['end'];
        $this->updateEnd = $config['updateEnd'];
        $this->listType = isset($config['listType']) ? $config['listType'] : 'html';
        $this->firstUrl = isset($config['firstUrl']) ? $config['firstUrl'] : '';
        $this->parseCss = isset($config['parseCss']) ? $config['parseCss'] : '';
        $this->dislocation = isset($config['dislocation']) ? $config['dislocation'] : 0;
        $this->domain = isset($config['domain']) && !empty($config['domain'])
            ? $config['domain'] : $this->taskConfig['domain'];
    }

    public function getList(){
        $this->urlReg = $this->domain. $this->urlReg;
        $listName = 'list_' . $this->catid . '.php'; //临时性列表url存放地址

        $urls = array();

        $end = EP_UPDATE_MODE ? $this->updateEnd : $this->end;
        for ($page = $this->start; $page <= $end; $page++) {

            $nowUrl = ($this->firstUrl && $page == $this->start)
                ? $this->domain.$this->firstUrl
                : str_replace('{d}', $page - $this->dislocation, $this->urlReg);

            $fileName = 'li_' . url2fileName( $nowUrl ) . '.html';
            $content = fetchUrl($nowUrl,
                EP_ROOT_PATH.'cache/html/'.EP_TASK_NAME.'/'.$fileName,
                $this->taskConfig['charset'], $this->taskConfig['domain'], $this->taskConfig['cacheList']);

            if($this->listType == 'html') {
                $urls = array_merge($urls, $this->handleHtml($content));
            } elseif($this->listType == 'json') {
                $urls = array_merge($urls, $this->handleJson($content));
            }


            echo("list ".$this->catid." page $page fetched.\n");

            if(EP_DEBUG_MODE) {
                $urls = array_values(array_unique($urls)); //去重
                if($this->taskConfig['reverseList']) {
                    $urls = array_reverse($urls); //反转
                }
                echo "nowUrl:\n";
                var_dump($nowUrl);
                echo "content:\n";
                var_dump($content);
                echo "urls:\n";
                var_dump($urls);
                echo("list debug mode.\n");
                writeCache($listName, $urls, EP_CACHE_PATH.'taskdata/'.EP_TASK_NAME.'/');
                exit;
            }
        }

        $urls = array_values(array_unique($urls)); //去重
        if($this->taskConfig['reverseList']) {
            $urls = array_reverse($urls); //反转
        }

        writeCache($listName, $urls, EP_CACHE_PATH.'taskdata/'.EP_TASK_NAME.'/');
        echo("list " .$this->catid ." done.\n");
    }

    private function handleHtml($content){
        require_once(EP_LIB_PATH.'simple_html_dom.php');
        $dom = str_get_html($content);

        if(!empty($this->parseCss)) {
            $links = $dom->find($this->parseCss);
        } else {
            $links = $this->parseList($dom);
        }


        $urls = $tmp = array();
        foreach ($links as $key => $value) {
            $tmp[0] = $value->href;
            $tmp[1] = $this->parseThumb($value, $key, $dom);
            $tmp[2] = $this->parseTitle($value, $key, $dom);
            $urls[] = implode('|', $tmp);
        }
        $dom->__destruct();
        return $urls;
    }

    abstract protected function handleJson($content);

    abstract protected function parseList($dom);

    abstract protected function parseThumb($a, $key, $dom);
}
