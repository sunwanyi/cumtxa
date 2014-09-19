<?php
/**
 * Class Data
 */
class Data {
    protected static $selfObjectArray;

    /**
     * 初始化对象
     */
    protected function __construct() {
        $this->onStart();
    }

    /**
     * 初始化函数，并返回当前类
     */
    public static function init(){
        $className=get_called_class();
        if(!isset(self::$selfObjectArray[$className])){
            self::$selfObjectArray[$className]=new $className();
        }
        return self::$selfObjectArray[$className];
    }

    /**
     * 在准备运行前调用
     * 注意：覆盖此函数时，建议调用父类方法（parent::onStart();）
     */
    protected function onStart(){
    }
}
class DataMessage {
    const STATE_SUCCESS=0;
    const STATE_INFO=1;
    const STATE_WARRING=2;
    const STATE_ERROR=3;

    public $state;
    public $message;
    public $title;

    function __construct($state, $title, $message=null) {
        $this->state = $state?:self::STATE_SUCCESS;
        if(!empty($title)) {
            $this->title = $title;
        }
        if(!empty($message)) {
            $this->message = $message;
        }
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * 设置返回消息，此函数将清除原本的message
     * @param $message string|array
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * 增加一个message到原本的内容里
     * @param $message
     */
    public function addMessage($message) {
        $this->message=(array)$this->message;
        $message=(array)$message;
        array_merge($this->message, $message);
    }

    public function setState($state) {
        $this->state = $state;
    }
}
