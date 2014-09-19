<?php
//namespace likyhPHPView;

function load_language($name){
    HTMLAnalysis::$objectNow->loadLanguage($name);
}
function e_url($set, $page, $action, $qus=null){
    HTMLAnalysis::$objectNow->echoUrl($set, $page, $action, $qus);
}
function e_page($page, $action, $qus=null){
    HTMLAnalysis::$objectNow->echoUrl(null, $page, $action, $qus);
}
function e_action($action, $qus=null){
    HTMLAnalysis::$objectNow->echoUrl(null, null, $action, $qus);
}
function e_qus($qus=null){
    HTMLAnalysis::$objectNow->echoUrl(null, null, null, $qus);
}

// 输出存在的变量
function e($varName, $collection=null){
    HTMLAnalysis::$objectNow->echoInCollection($varName, $collection);
}
// 以整数类型输出变量
function e_int($var){
    HTMLAnalysis::$objectNow->echoInt($var);
}
function e_implode($var){
    HTMLAnalysis::$objectNow->echoImplode($var);
}

function import_tpl($file){
    HTMLAnalysis::$objectNow->importTpl($file);
}
/**
 * HTML内容分析
 * @author linyh
 */
//TODO 未做容错处理
//TODO 以后加上编译功能
class HTMLAnalysis {//implements HTMLAnalysisInterface {
    private static $objectStack=array();
    /**
     * @var HTMLAnalysis
     */
    public static $objectNow;
    /**
     * @var appinfo
     */
    protected $appInfo;
    private $result;
    private $system;
    private $language;

    private static $root;
    //
    private $folderArray;
    private $getArray;

    private function __construct(){
        $this->appInfo=&getAppInfo();
        self::$root=$this->appInfo->system['root'];
        $this->result=array();
        $this->system=$this->appInfo->system;

        // TODO 载入默认语言的代码放在这里不太合适
        $this->language=array();
        $defaultLanguage=getConfig("language", "default");
        if(!empty($defaultLanguage)){
            foreach(explode(",",$defaultLanguage) as $v){
                $this->loadLanguage($v);
            }
        }
    }

    /**
     * 新建、初始化并返回当前类
     * @return \HTMLAnalysis
     */
    public static function init(){
        self::$objectNow= new HTMLAnalysis();
        array_push(self::$objectStack,self::$objectNow);
        return self::$objectNow;
    }

    /**
     * 设置url目录
     * @param $folderArray
     * @return bool
     */
    public function setFolder($folderArray){
        $this->folderArray=$folderArray;
        return true;
    }

    public function setGet($getArray){
        $this->getArray=$getArray;
        return true;
    }

    /**
     * 释放当前类
     * @return bool
     */
    public static function release(){
        self::$objectNow=array_pop(self::$objectStack);
        return true;
    }

    private function formatHtml($disPath){
        $r=&$this->result;
        $result=&$this->result;
        $l=&$this->language;
        $language=&$this->language;
        $s=&$this->system;
        $system=&$this->system;

        include $disPath;
    }

    public function importTpl($file){
        $this->formatHtml("./site/".$file);
    }

    /**
     * 显示内容
     * @param &$result
     * @param $disPath
     * @return boolean 是否执行成功
     */
    public function display(&$result,$disPath){
        if(!is_array($result)){
            trigger_error("\$result 参数不是一个数组",E_USER_NOTICE);
            $this->result=(array)$result;
        }else{
            $this->result=$result;
        }
        if(!is_file($disPath)){
            trigger_error("引用了不存在的template文件:".$disPath,E_USER_ERROR);
            return false;
        }
        $this->formatHtml($disPath);
        self::release();
        return true;
    }

    /**
     * 快速生成符合标准的url
     * @param $folderArray array 路径数组
     * @param mixed $qus get参数，可以是一个字符串或者一个数组
     * @return string
     */
    public function getURL($set=null, $page=null, $action=null, $qus=null){
        $wr=WebRouter::init();
        return $wr->getURL($set, $page, $action, $qus);
    }
    public function echoUrl($set=null, $page=null, $action=null, $qus=null){
        echo $this->getURL($set, $page, $action, $qus);
    }

    // 以下是以已存储的变量名作为参数
    // 输出存在的变量
    public function echoInCollection($varName, $collection="result"){
        switch($collection){
            case 'l':
            case 'language':
                $var=isset($this->language[$varName])?$this->language[$varName]:'';
                break;
            case 's':
            case 'system':
                $var=isset($this->system[$varName])?$this->system[$varName]:'';
                break;
            default:
            case 'result':
                $var=isset($this->result[$varName])?$this->result[$varName]:'';
                break;
        }
        echo $var;
    }

    /**
     * 暂入一个语言文件
     * @param $name
     */
    public function loadLanguage($name){
        if(!isset($this->language[$name])){
            $this->language[$name]=analysisIniFile("./site/language/{$name}.language.ini");
        }
    }
    // 以整数类型输出变量
    public function echoInt($var){
        if(isset($var)){
            echo (int)($var);
        }
    }
    // 把一个数组连接起来输出
    public function echoImplode($var){
        if(isset($var)&&is_array($var)){
            echo implode(",", $var);
        }
    }
}