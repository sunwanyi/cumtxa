<?php
/**
 * 便于操作的session类
 * 使用着一个类可以自动帮你解决很多安全性和性能问题
 * 并且操作和普通类区别不大
 */
class SimpleSession implements ArrayAccess,Countable {
    /**
     * HTTP cache control
     * @const string
     */
    const CACHE_NOCACHE = 'nocache';
    const CACHE_PUBLIC = 'public';
    const CACHE_PRIVATE = 'private';
    const CACHE_PRIVATE_NO_EXPIRE = 'private_no_expire';

    const SESSION_LAST_ACCESS='sessionLastAccess';

    protected static $selfObject;
    protected $sessionName;
    protected $sessionId;
    protected $sessionTime;// 过期时间

    /**
     * 初始化函数，并返回当前类
     * @param string $sessionName 设置session保存在cookie的位置
     * @param string $sessionId 设置session的标志名
     * @return SimpleSession
     */
    public static function init($sessionName=null, $sessionId=null){
        if(!isset(self::$selfObject)){
            self::$selfObject=new SimpleSession($sessionName, $sessionId);
        }
        return self::$selfObject;
    }
    /**
     * 重新打开session类
     */
    public static function reinit(&$appInfo, $sessionName=null, $sessionId=null){
        if(isset(self::$selfObject)){
            self::release();
        }
        return self::init($appInfo, $sessionName, $sessionId);
    }

    /**
     * 设置session保存在cookie的位置和session的标志名
     * @param string|null $sessionName
     * @param string|null $sessionId
     */
    protected function __construct($sessionName=null, $sessionId=null){
        if(!empty($sessionName)){
            session_name($sessionName);
            $this->sessionName=$sessionName;
        }
        if(!empty($sessionId)){
            session_id($sessionId);
            $this->sessionId=$sessionId;
        }
        session_start();
    }

    /**
     * 重新设置session id
     * @param string $sessionId 要设置的id
     */
    public function setSessionId($sessionId){
        session_id($sessionId);
        if(ini_get('session.use_cookies'))
            setcookie('PHPSESSID', session_id(), NULL, '/');
    }

    /**
     * 重新设置session id
     * @param string $sessionId 要设置的id
     */
    private function regenerateSessionId($sessionId=null){
        //TODO 未完成
    }

    /**
     * @param $time int 过期时间
     */
    public function setTime($time){
        $this->sessionTime=$time;
        ini_set('session.gc_maxlifetime', $time);
    }

    /**
     * 获取Session值
     * @param string $name Session名称
     * @return mixed
     */
    public function get($name){
        return $_SESSION[$name];
    }

    /**
     * 设置Session
     * @param string $name Session名称
     * @param string $value 值
     */
    public function set($name,$value){
        $_SESSION[$name] = $value;
    }

    /**
     * 释放当前类
     */
    public static function release(){
        session_unset();
        session_destroy();
        self::$selfObject=null;
    }

    public function offsetExists($offset){
        return isset($_SESSION[$offset]);
    }

    public function offsetGet($offset){
        return $_SESSION[$offset];
    }

    public function offsetSet($offset, $value){
        $_SESSION[$offset]=$value;
    }

    public function offsetUnset($offset){
        unset($_SESSION[$offset]);
    }

    public function count(){
        count($_SESSION);
    }
}