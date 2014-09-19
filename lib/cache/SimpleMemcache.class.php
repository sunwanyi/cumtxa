<?php
if(!function_exists("memcache_init")){
    /**
     * 返回一个memcache的资源
     */
    function memcache_init(){
        // 在这个函数里面要做的是连接memcache服务器
        return null;
    }
}
class SimpleMemcache implements ArrayAccess {
    /**
     * 用于记录保存的id的集合
     * @var string
     */
    protected $set;
    protected $site;
    protected $mmc;

    /**
     * @param string $set 要缓存的数据的分类
     */
    public function __construct($set){
        $this->set=$set;
        $this->site=getConfig("site","name");
        $this->mmc=memcache_init();
    }

    protected function keyHash($key){
        return getHash($this->site,$this->set,$key);
    }

    public function get($key){
        return memcache_get($this->mmc,$this->keyHash($key));
    }
    /**
     * 设置缓存
     * @param $key
     * @param $value
     * @param int $timeout 过期的时间，可以为秒数或者unix时间戳
     */
    public function set($key,$value,$timeout=0){
        memcache_set($this->mmc, $this->keyHash($key), $value, MEMCACHE_COMPRESSED, $timeout);
    }

    /**
     * 删除某一个特定的
     * @param $key
     * @param int $timeout 设置删除的时间
     */
    public function delete($key, $timeout=0){
        memcache_delete($this->mmc,$key, $timeout);
    }
    /**
     * 删除所有数据
     */
    public function flush(){
        memcache_flush($this->mmc);
    }

    public function offsetExists($offset){
        return $this->get($offset);
    }

    public function offsetGet($offset){
        return $this->get($offset);
    }

    public function offsetSet($offset, $value){
        $this->set($offset, $value);
    }

    public function offsetUnset($offset){
        $this->delete($offset);
    }
}