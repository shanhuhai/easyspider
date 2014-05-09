<?php 

class TaskPage extends SpiderPage{

    public function __construct($config = array()){
        global $settings;
        $this->taskConfig = $settings;
    }
    
    protected function get_tags($dom, $data, $content, $nowUrl){
        $return = $dom->find('.tags a', 0)->text();
        $return = str_replace('tags', '', $return);
        $return = trim($return);
        return $return;
    }
    
    protected function get_content($dom, $data, $content, $nowUrl){
        $return = $dom->find('#art_content', 0)->innertext();
        $return = saveContentPic($return, $this->taskConfig['domain']);
        $return = strip_tags($return, '<p><br><img>');
        $return = trim($return);
        return $return;
    }

    protected function checkExists($title, $dom, $content, $nowUrl){
        return false;
    }

    protected function saveData($data, $dom){

        unset($data['thumb']);
        unset($data['thumb_source']);
        $fields = '`'.implode('`,`', array_keys($data)).'`';

        foreach($data as &$value){
            $value = "'".addslashes($value)."'";
        }
        $values = implode(',', $data);

        $db = $this->taskConfig['dbConfig'];
        $lnk = mysql_connect($db['host'], $db['username'], $db['password']);

        if(empty($lnk)) {
            logInfo('Master Db Not connected : ' . mysql_error(), 'ERROR');
        }
        mysql_query("set names utf8", $lnk);
        mysql_select_db($db['dbname'], $lnk);


        $sql = "INSERT INTO `spider` ($fields) VALUES($values)";
        $result = mysql_query($sql);
        if(!$result){
            logInfo($sql.':'.mysql_error()."\n", 'ERROR');
        } else {
            echo $data['title']." success\n";
        }
        mysql_close($lnk);
    }


}
