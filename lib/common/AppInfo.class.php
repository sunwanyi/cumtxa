<?php
/**
 * @author linyh
 *
 */
class AppInfo {
    /**
     * 网站根目录
     * @var string
     */
    public $root;
    /**
     * 请求地址数组
     * @var array
     */
    public $folderArray;
    /**
     * 请求的action
     * @var string
     */
    public $action;
    /**
     * 配置数组
     * @var array
     */
    public $config;
    public $system;
    /** @var WebRouter */
    public $webRouter;

    function __construct(){
        $this->system['root']=getConfig('site','root');
        $this->system['siteRoot']=$this->system['root']."site/";
    }

    /**
     * 获取运行的根目录
     */
    public static function getRoot(){
        return self::$root;
    }

    /**
     * 快速生成符合标准的url
     * @param $folderArray array 路径数组
     * @param mixed $qus get参数，可以是一个字符串或者一个数组
     * @return string
     */
    public function getURL($folderArray, $qus=null){
        $path=$this->root.implode("/",$folderArray);
        $qusStr='';
        if(!empty($qus)){
            if(is_array($qus)){
                $qusStr = "?".implode("&",array_map(
                        create_function('$key, $value', 'return "{$key}={$value}";'),
                        array_keys($qus), array_values($qus)));
            }else{
                $qusStr="?".$qus;
            }
        }
        return $path.$qusStr;
    }

    /**
     * 获取ip 一个字符串
     */
    public static function getIp(){
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }elseif(!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        }else{
            return false;
        }
        return $cip;
    }
}

?>