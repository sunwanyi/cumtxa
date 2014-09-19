<?php
/**
 * sae平台上使用的key-value数据库，
 * 相比于session，可以长久保存
 */
class KvDB implements ArrayAccess {
    protected $seaKV;
    protected static $selfObject;
    protected $appInfo;

    /**
     * 初始化函数，并返回当前类
     * @return KvDb
     */
    public static function init(){
        if(!self::$selfObject){
            self::$selfObject=new KvDb();
        }
        return self::$selfObject;
    }
    protected function __construct(){
        $this->seaKV = new SaeKV();
        $this->seaKV->init();
    }

    /**
     * 添加一个键值
     * @param string $key key键名
     * @param string $value 值
     */
    public function add($key,$value){
        return $this->seaKV->add($key,$value);
    }

    /**
     * 删除一个记录
     * @param string $key key键名
     */
    public function delete($key){
        return $this->seaKV->delete($key);
    }

    /**
     * 通过前缀搜索值
     * @param $prefix_key Key值的前缀
     * @param int $count
     * @param string $start_key
     * @return mixed
     */
    public function getMulPrefix($prefix_key, $count, $start_key=''){
        return $this->seaKV->pkrget($prefix_key, $count, $start_key);
    }

    /**
     * 获取一个记录
     * @param string|array $key key键名
     * @return array|string|false
     */
    public function get($keys){
        if(is_array($keys)){
            return $this->seaKV->mget($keys);
        }else{
            return $this->seaKV->get($keys);
        }
    }

    /**
     * 添加\修改一条记录
     * @param string $key 键名
     * @param string $value 值
     */
    public function set($key,$value){
        return $this->seaKV->set($key,$value);
    }

    /**
     * 替换一个键值，替换成功返回true
     * 如果原本不存在，则返回false
     * @param string $key 键名
     * @param string $value 值
     */
    public function replace($key,$value){
        return $this->seaKV->replace($key, $value);
    }

    /**
     * 释放当前类
     */
    public static function release(){
        self::$selfObject=null;
    }

    public function offsetExists($offset){
        return $this->seaKV->get($offset);
    }

    public function offsetGet($offset){
        if(is_array($offset)){
            return $this->seaKV->mget($offset);
        }else{
            return $this->seaKV->get($offset);
        }
    }

    public function offsetSet($offset, $value){
        $this->seaKV->set($offset, $value);
    }

    public function offsetUnset($offset){
        return $this->seaKV->delete($offset);
    }
}