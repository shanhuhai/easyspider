<?php
class TaskList extends SpiderList{
    public function __construct($config = array()){
        global $settings;
        $this->taskConfig = $settings;
        parent::__construct($config);
    }

    protected function handleJson($content){

    }

    protected function parseList($dom){
        $return = $dom->find('.newslist dt a');
        return $return;
    }

    protected function parseTitle($a, $key, $dom){
        $return =  $a->innertext();
        return $return;

    }


    protected function parseThumb($a, $key, $dom){
        return '';
        /*
        $return =  $a->find('img', 0)->src;
        return $return;
        */
    }
}
