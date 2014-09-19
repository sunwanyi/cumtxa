<?php
/** 
 * 标准库中组件的基础类
 * @author linyh
 */
class View {
    protected static $selfObjectArray;

    protected function __construct() {
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

    public static function displayAsHtml($result, $tplFile=null, $echo=true){
        import("HTMLAnalysis");
        $ha=HTMLAnalysis::init();
        if($echo){
            $ha->display($result, "./site/".$tplFile);
            return true;
        }else{
            ob_start();
            $ha->display($result, "./site/".$tplFile);
            $s=ob_get_clean();
            return $s;
        }
    }
    public static function displayAsJson($result){
        $time=time();
        $result['serverTime']=$time;
        $result['serverDate']=date("Y-m-d H:i:s", $time);

        if(isset($_GET["callback"])&& preg_match("/^[\w_]+$/", $_GET["callback"])) {
            echo htmlspecialchars($_GET["callback"]) . '(' . json_encode($result) . ')';
        }else{
            echo json_encode($result);
        }
    }
    public static function displayAsEcho($result){
        echo $result;
    }
    public static function displayAsDump($result){
        var_dump($result);
    }
}