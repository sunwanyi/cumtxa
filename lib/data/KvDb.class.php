<?php
/**
 * key-value数据库，
 * 相比于session，可以长久保存
 * TODO 当前使用文件实现，仅用于简单的测试，在正式服务器上讲使用redis实现
 */
import("PathGeneration");
class KvDB implements ArrayAccess {
    protected $cacheFile;
    protected static $selfObject;
    protected $data;

    /**
     * 初始化函数，并返回当前类
     * @return KvDB
     */
    public static function init(){
        if(!self::$selfObject){
            self::$selfObject=new KvDb();
        }
        return self::$selfObject;
    }
    protected function __construct(){
        $cachePath=PathGeneration::getFolder("./cache/kvdb/");
        $this->cacheFile=$cachePath."local.tmp";
        if(is_readable($this->cacheFile)){
            $this->data=json_decode(file_get_contents($this->cacheFile),true);
        }else{
            $this->data=array();
        }
    }

    /**
     * 添加一个键值
     * @param string $key key键名
     * @param string $value 值
     */
    public function add($key,$value){
        if(isset($this->data[$key])){
            return false;
        }
        $this->data[$key]=$value;
        fwrite(fopen($this->cacheFile,"w"),json_encode($this->data));
        return true;
    }

    /**
     * 删除一个记录
     * @param string $key key键名
     */
    public function delete($key){
        if(!isset($this->data[$key])){
            return false;
        }
        unset($this->data[$key]);
        fwrite(fopen($this->cacheFile,"w"),json_encode($this->data));
        return true;
    }

    /**
     * 通过前缀搜索值
     * @param $prefix_key Key值的前缀
     * @param int $count
     * @param string $start_key
     * @return mixed
     */
    public function getMulPrefix($prefix_key, int $count, $start_key=''){
        // TODO 未实现
    }

    /**
     * 获取一个记录
     * @param string|array $key key键名
     * @return array|string|false
     */
    public function get($keys){
        $result=array();
        foreach((array)$keys as $value){
            if(isset($this->data[$value])){
                array_push($result, $this->data[$value]);
            }
        }
        return $result;
    }

    /**
     * 添加\修改一条记录
     * @param string $key Session名称
     * @param string $value 值
     */
    public function set($key,$value){
        $this->data[$key]=$value;
        fwrite(fopen($this->cacheFile,"w"),json_encode($this->data));
        return true;
    }

    /**
     * 替换一个键值，替换成功返回true
     * 如果原本不存在，则返回false
     * @param string $key Session名称
     * @param string $value 值
     */
    public function replace($key,$value){
        if(!isset($this->data[$key])){
            return false;
        }
        $this->data[$key]=$value;
        fwrite(fopen($this->cacheFile,"w"),json_encode($this->data));
        return true;
    }

    /**
     * 释放当前类
     */
    public static function release(){
        self::$selfObject=null;
    }

    public function offsetExists($offset){
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset){
        $result=array();
        foreach((array)$offset as $value){
            if(isset($this->data[$value])){
                array_push($result, $this->data[$value]);
            }
        }
//        var_dump((array)$offset);
        return $result;
    }

    public function offsetSet($offset, $value){
        $this->data[$offset]=$value;
        fwrite(fopen($this->cacheFile,"w"),json_encode($this->data));
    }

    public function offsetUnset($offset){
        unset($this->data[$offset]);
        fwrite(fopen($this->cacheFile,"w"),json_encode($this->data));
        return true;
    }
}