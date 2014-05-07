
    protected function saveData($data, $dom){

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


        $sql = "INSERT INTO `***EDITME***` ($fields) VALUES($values)";
        $result = mysql_query($sql);
        if(!$result){
            logInfo($sql.':'.mysql_error()."\n", 'ERROR');
        } else {
            echo $data['title']." success\n";
        }
        mysql_close($lnk);
    }
