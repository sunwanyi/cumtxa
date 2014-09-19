<?php
import("PathGeneration");
import("FileLoader");
/**
 * 系统缓存基于文件系统 TODO 应该强制先check在getCache和Save
 * Class HtmlCache
 */
class HtmlCache implements HtmlCacheInterface{
    /**
     * $cacheId和$cacheDigest是用来记录针对哪一个缓存，可以在check前重新设置
     * @var
     */
    protected $cacheId;
    protected $cacheDigest;
    protected $cacheTime;
    protected $cacheContent;
    protected $ifGetByEcho=false;
    protected $cachePath;
    protected $cacheInfo;

    protected function __construct($cacheId, $cacheDigest=null){
        $this->cacheId=$cacheId;
        $this->cacheDigest=isset($cacheDigest)&&!empty($cacheDigest)?md5($cacheDigest):'default';
        $this->cachePath=PathGeneration::getFolder("./cache/html/");
    }

    /**
     * 初始化
     * @param $cacheId 缓存ID
     * @param $cacheDigest 根据缓存签名不同可以有多个文件
     * @return \HtmlCache 返回一个缓存类
     */
    public static function init($cacheId, $cacheDigest=null){
        return new HtmlCache($cacheId, $cacheDigest);
    }

    /**
     * @param string $cacheDigest
     */
    public function setCacheDigest($cacheDigest){
        $this->cacheDigest = isset($cacheDigest)&&!empty($cacheDigest)?md5($cacheDigest):'default';
    }

    /**
     * 检测某一个变量是否缓存，根据时间判断是否过期
     * @return bool
     */
    public function check(){
        $ifCache=false;// 记录是否已经缓存的变量

        // 读取缓存信息
        if(empty($this->cacheInfo)){
            $cacheInfoFile=new FileLoader("{$this->cachePath}{$this->cacheId}_info.tmp", array("mode"=>"read"));
            if($cacheInfoFile->isReadable()){
                $this->cacheInfo=json_decode($cacheInfoFile->getFileString(),true);
                $cacheInfoFile->close();
            }
        }
        // 检测对应签名的缓存是否过期
        if(isset($this->cacheInfo[$this->cacheDigest])){
            $ifCache=time()<$this->cacheInfo[$this->cacheDigest]['expiredTime'];
        }
        return $ifCache;
    }

    /**
     * @param bool $ifEcho 是否直接输出
     * @return string null
     */
    public function getCache($ifEcho=self::INDIRECT_ECHO_CACHE){
        $file="";
        $cacheInfoFile=new FileLoader("{$this->cachePath}{$this->cacheId}/{$this->cacheDigest}.tmp", array("mode"=>"read"));
        if($cacheInfoFile->isReadable()){
            $file=$cacheInfoFile->getFileString();
            $cacheInfoFile->close();
        }else{
            trigger_error("缓存文件无法读取：{$this->cachePath}{$this->cacheId}/{$this->cacheDigest}.tmp",E_USER_WARNING);
        }
        if($ifEcho){
            echo $file;
            return null;
        }else{
            return $file;
        }
    }

    /**
     * 直接输出缓存
     */
    public function echoCache(){
        $this->getCache(self::DIRECT_ECHO_CACHE);
    }

    /**
     * 开始保存输出缓存
     */
    public function setContentByEcho(){
        ob_start();
        $this->ifGetByEcho=true;
    }

    /**
     * 设置缓存内容
     * @param mixed $value
     */
    public function setContentByValue($value){
        $this->ifGetByEcho=false;
        $this->cacheContent=$value;
    }

    /**
     * 设置缓存时间，不设置就是当天23:59:59过期
     * @param $cacheTime
     */
    public function setCacheTime($cacheTime=null){
        $this->cacheTime=strtotime(isset($cacheTime)&&!empty($cacheTime)?$cacheTime:self::DEFAULT_TIME_STRING);
    }

    /**
     * 缓存获取与设置结束，存入文件或者内存
     * @param null $cacheTime
     * @return bool
     */
    public function cacheSave($cacheTime=null){
        if(isset($cacheTime)&&!empty($cacheTime)) $this->setCacheTime($cacheTime);
        if($this->ifGetByEcho){
            // 获取输出缓冲数据
            $this->cacheContent=ob_get_contents();
            ob_end_flush();
        }

        // 更新内存中的缓存信息
        $this->cacheInfo[$this->cacheDigest]['expiredTime']=$this->cacheTime;
        $this->cacheInfo[$this->cacheDigest]['expiredDateTime']=date("Y-m-d H:i:s",$this->cacheTime);

        $ifSave=false;
        // 缓存文件地址
        $cacheContentFile=new FileLoader(
            PathGeneration::getFolder("{$this->cachePath}{$this->cacheId}")."{$this->cacheDigest}.tmp",
            array("mode"=>"write"));
        $cacheInfoFile=new FileLoader("{$this->cachePath}{$this->cacheId}_info.tmp", array("mode"=>"write"));
        if($cacheInfoFile->isWritable()){
            if($cacheContentFile->isWritable()){
                $cacheInfoFile->write(json_encode($this->cacheInfo));
                $cacheContentFile->write($this->cacheContent);
                $cacheContentFile->close();
                $ifSave=true;
            }
            $cacheInfoFile->close();
        }
        return $ifSave;
    }
}

/**
 * Class HtmlCacheConfig
 */
class HtmlCacheConfig {
    public $id;
    public $digest;
    public $time;
}