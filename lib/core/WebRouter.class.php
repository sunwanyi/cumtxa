<?php
/**
 * 系统web请求分析类
 * @author linyh
 */
class WebRouter implements WebRouterInterface {
    protected $commonPath;
    protected static $selfObject;

    protected $ifAnalysis=false;
    protected $appInfo;
    protected $getArray;
    protected $folderArray;
    protected $action;
    protected $postArray;
    protected $root;

    /**
     * 根据配置文件清理url
     * @param $requestStr 要清理的url
     * @return string
     */
    protected function urlClean($requestStr){
        if($requestStr[0]=="/") $requestStr=substr($requestStr, 1);
        if(substr($requestStr, -1)=="/") $requestStr=substr($requestStr, 0, -1);
        $requestStr=substr($requestStr, strlen($this->commonPath));
        if($requestStr[0]=="/") $requestStr=substr($requestStr, 1);
        return $requestStr;
    }

    /**
     * 构造函数，检查配置是否完成
     */
    protected function __construct(){
        assert(isset($GLOBALS['config']['site']["commonPath"]));
        $this->appInfo=&getAppInfo();
        $this->commonPath=$GLOBALS['config']['site']["commonPath"];
        $this->root=$this->appInfo->system['root'];
        $this->ifAnalysis=false;
    }

    /**
     * 初始化函数，并返回当前类
     * @return WebRouter
     */
    public static function init(){
        if(!self::$selfObject){
            self::$selfObject=new WebRouter();
        }
        return self::$selfObject;
    }

    /**
     * 执行这个路由并分析出指向的类
     */
    public function exec(){
        if(isset($_SERVER["HTTP_X_ORIGINAL_URL"])&& !empty($_SERVER["HTTP_X_ORIGINAL_URL"])){
            $URI=$_SERVER["REQUEST_URI"];
        }elseif(isset($_SERVER["REQUEST_URI"])&& !empty($_SERVER["REQUEST_URI"])){
            $URI = $_SERVER["REQUEST_URI"];
        }else{
            trigger_error("http请求中没有REQUEST_URI",E_USER_ERROR);
        }
        $requestStr=$this->urlClean($URI);

        if(strpos($requestStr,"?")!==false){
            $folderStr=substr($requestStr, 0, strpos($requestStr,"?"));// 请求地址字符串
        }else{
            $folderStr=$requestStr;
        }

        //分析目录
        $this->folderArray=!empty($folderStr)?explode("/",$folderStr):array();
        $this->appInfo->action=$this->action=count($this->folderArray)>1?array_pop($this->folderArray):null;
        $this->appInfo->folderArray=$this->folderArray;
        //分析get和post
        $this->getArray=$_GET;
        $this->postArray=$_POST;
        return $this->ifAnalysis=true;
    }

    /**
     * 获取GET索引数组
     * @return array
     */
    public function getGet(){
        if($this->ifAnalysis&&is_array($this->getArray)){
            return $this->getArray;
        }else{
            return array();
        }
    }

    /**
     * 获取含有post的数组
     * @return array
     */
    public function getPost(){
        if($this->ifAnalysis&&is_array($this->postArray)){
            return $this->postArray;
        }else{
            return array();
        }
    }

    /**
     * 返回router解析出来的
     * @return ObjectInterface
     */
    public function getTransformClass() {
        $folderArray=$this->folderArray;
        if(count($folderArray)>0){
            $classSpacePath="Custom.".implode(".",$folderArray);
            $page=array_pop($folderArray);
        }else{
            $classSpacePath=getConfig('site', 'defaultSpacePath');
            $page=getConfig('site', 'defaultPage');
        }
        if(import($classSpacePath)){
            return $page::init();
        }else{
            //TODO 这里需要改变，应该改成触发一个错误事件
            trigger_error("未找到该对象",E_USER_NOTICE);
            return null;
        }
    }

    private function paramImplode($param){
        return implode("&",array_map(
            create_function('$key, $value', 'return urlencode($key)."=".urlencode($value);'),
            array_keys($param), array_values($param)));
    }

    /**
     * 生成这个类可以解析的url
     * @param null|string $set set字符串
     * @param null|string $page page字符串
     * @param null|string $action action字符串
     * @param null|string|array $qus get参数，可以是一个字符串或者一个数组
     * @return string
     */
    public function getURL($set=null, $page=null, $action=null, $qus=null){
        $path=$this->root;
        $folderArray=$this->folderArray;
        if(empty($page)){
            $page=array_pop($folderArray);
        }else{
            array_pop($folderArray);
        }
        if(!empty($set)) $folderArray=explode("/",$set);
        array_push($folderArray, $page, !empty($action)?$action:$this->appInfo->action);
        $path.= implode("/",$folderArray);

        $qusStr='?';
        if(!empty($qus)){
            $qusStr .= is_array($qus)?$this->paramImplode($qus):$qus;
            return $path.$qusStr;
        }else{
            return $path;
        }
    }
    public function getPage($page=null, $action=null, $qus=null){
        return $this->getURL(null,$page,$action,$qus);
    }
    public function getAction($action=null, $qus=null){
        return $this->getURL(null,null,$action,$qus);
    }
    public function getQuestion($qus=null){
        return $this->getURL(null,null,null,$qus);
    }
    public function getAbsolute($basePath){
        return $this->root.$basePath;
    }

    /**
     * 释放当前类
     */
    public static function release(){
        self::$selfObject=null;
    }
}