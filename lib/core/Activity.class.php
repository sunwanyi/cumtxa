<?php
/** 
 * 标准库中组件的基础类，这是最简单就可以使用的Activity
 * @author linyh
 */
class Activity implements ActivityInterface{
    /**
     * @var AppInfo
     */
    protected $appInfo;
    protected static $selfObjectArray;
    protected function __construct() {
        $this->onStart();
    }
    /**
     * 初始化函数，并返回当前类
     * @return mixed
     */
    public static function init(){
        $className=get_called_class();
        if(!isset(self::$selfObjectArray[$className])){
            self::$selfObjectArray[$className]=new $className();
        }
        return self::$selfObjectArray[$className];
    }

    /**
     * 运行该函数
     * @param null/string $action
     */
    public function exec($action=null){
        if(!empty($action)){
            $this->devolve($action."Task");
        }else{
            $this->devolve(getConfig("site","defaultAction")."Task");
        }
    }
    public function devolve($action){
        $action=$this->getAction($action);
        $this->$action();
    }

    /**
     * 返回action对应的实际函数名
     * @param string $action
     * @return string 实际函数名
     */
    protected function getAction($action){
        if(!method_exists($this,$action)){
            trigger_error("方法($action)不存在",E_USER_ERROR);
        }
        return $action;
    }

    /**
     * 释放当前类
     */
    public static function release(){
        $className=get_called_class();
        unset(self::$selfObjectArray[$className]);
        return;
    }

    /**
     * 在准备运行前调用
     * 注意：覆盖此函数时，建议调用父类方法（parent::onStart();）
     */
    protected function onStart(){
    }
}