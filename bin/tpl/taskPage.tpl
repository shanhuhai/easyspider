
class TaskPage extends SpiderPage{

    public function __construct($config = array()){
        global $settings;
        $this->taskConfig = $settings;
    }
    <?php echo $callbackFuncStr ?>


}
