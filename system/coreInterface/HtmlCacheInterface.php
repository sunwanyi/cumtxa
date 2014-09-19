<?php
/**
 * 缓存系统标准
 * Interface HtmlCacheInterface
 */
interface HtmlCacheInterface {
    const DIRECT_ECHO_CACHE=true;
    const INDIRECT_ECHO_CACHE=false;
    const DEFAULT_TIME_STRING="today 23:59";

    /**
     * 初始化
     * @param $cacheId 缓存ID
     * @param $cacheDigest 根据缓存签名不同可以有多个文件
     * @return HtmlCacheInterface 返回一个缓存类
     */
    public static function init($cacheId, $cacheDigest=null);

    /**
     * 检测某一个变量是否缓存，根据时间判断是否过期
     * @return bool
     */
    public function check();

    /**
     * @param bool $ifEcho 是否直接输出
     * @return string
     */
    public function getCache($ifEcho=self::INDIRECT_ECHO_CACHE);

    /**
     * 直接输出缓存
     */
    public function echoCache();

    /**
     * 开始保存输出缓存
     */
    public function setContentByEcho();

    /**
     * 设置缓存内容
     * @param mixed $value
     */
    public function setContentByValue($value);

    /**
     * 设置缓存时间，不设置就是当天23:59:59过期
     * @param $cacheTime
     */
    public function setCacheTime($cacheTime=null);

    /**
     * 缓存获取与设置结束，存入文件或者内存
     * @param null $cacheTime
     * @return bool
     */
    public function cacheSave($cacheTime=null);

}