<?php

class View {
    public  $dir;
    public  $file;
    public  $ext = '.php';
    public static $instance = array();

    protected $vars;

    public function __construct(array $config = array()){
        if (!empty($config)) {
            foreach ($config as $key => $value) {
                $this->{$key} = $value;
            }
        }
        $this->cleanVars();
    }

    public static function getInstance($config){
        $key = md5(json_encode($config));
        if(!isset(self::$instance[$key]) ){
            self::$instance[$key] = new self($config);
        }
        return self::$instance[$key];
    }

    function setView($view){
        $this->file = $this->dir .$view .$this->ext;
        return $this;
    }

    function setDir($dir){
        $this->dir = $dir;
        return $this;
    }

    function assign($key, $data = null){
        if (is_array($key)) {
            $this->vars = array_merge($this->vars, $key);
        } elseif (is_object($key)) {
            $this->vars = array_merge($this->vars, (array)$key);
        } else {
            $this->vars[$key] = $data;
        }
        return $this;
    }

    function cleanVars(){
        $this->vars = array();
        return $this;
    }

    function display($view){
        echo $this->fetch($view);
    }

    function fetch($view){
        $this->setView($view);
        $this->beforeRender($view);
        if (!empty($_REQUEST)) {
            extract($_REQUEST);
        }
        if (!empty($this->vars)){
            extract($this->vars);
        }
        ob_start();
        try {
            include $this->_file();
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
        $output = ob_get_clean();
        $this->afterRender($output);
        return $output;
    }

    protected function beforeRender($view){
    }

    protected function afterRender(& $output){
    }

    protected function _file(){
        return $this->file;
    }
}