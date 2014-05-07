
    protected function get_content($dom, $data, $content, $nowUrl){
        $return = $dom->find('', 0)->innertext();
        $return = saveContentPic($return, $this->taskConfig['domain']);
        $return = trim($return);
        return $return;
    }
