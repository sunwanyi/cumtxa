<?php
/**
 * bae使用的redis
 */
class KvDb implements ArrayAccess {
    protected $redis;
    protected static $selfObject;

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
        /*从平台获取数据库名*/
        $dbname = getConfig("redis","dbname");

        /*从环境变量里取host,port,user,pwd*/
        $host = getConfig("redis","host");
        $port = getConfig("redis","port");
        $user = getConfig("redis","user");
        $pwd = getConfig("redis","pwd");

        $this->redis = new Redis();
        $this->redis->connect($host, $port);
        $this->redis->auth($user . "-" . $pwd . "-" . $dbname);
    }

    /**
     * 添加一个键值
     * @param string $key key键名
     * @param string $value 值
     * @return bool
     */
    public function add($key,$value){
        return $this->redis->set($key,$value);
    }

    /**
     * 删除一个记录
     * @param string $key key键名
     */
    public function delete($key){
        $this->redis->delete($key);
    }

    /**
     * 获取一个记录
     * @param string|array $keys key键名
     * @return array|string|false
     */
    public function get($keys){
        if(is_array($keys)){
            return $this->redis->getMultiple($keys);
        }else{
            return $this->redis->get($keys);
        }
    }

    /**
     * 添加\修改一条记录
     * @param string $key 键名
     * @param string $value 值
     * @return bool
     */
    public function set($key,$value){
        return $this->redis->set($key,$value);
    }

    /**
     * 替换一个键值
     * @param string $key 键名
     * @param string $value 值
     * @return bool
     */
    public function replace($key,$value){
        return $this->redis->set($key, $value);
    }

    /**
     * 释放当前类
     */
    public static function release(){
        self::$selfObject=null;
    }

    public function offsetExists($offset){
        return $this->redis->get($offset);
    }

    public function offsetGet($offset){
        if(is_array($offset)){
            return $this->redis->mget($offset);
        }else{
            return $this->redis->get($offset);
        }
    }

    public function offsetSet($offset, $value){
        $this->redis->set($offset, $value);
    }

    public function offsetUnset($offset){
        $this->redis->delete($offset);
    }
}