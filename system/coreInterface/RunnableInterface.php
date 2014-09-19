<?php
/**
 * 可运行的类必须实现的接口
 * @author linyh
 */
interface RunnableInterface {
    /**
     * 新建类
     * @return RunnableInterface
     */
    public static function init();

    /**
     * 运行该函数，该函数应该要可以接受任意多个变量
     * @param[] mixed $paramN
     * @return
     */
    public function exec();

    /**
     * 释放当前类
     */
    public static function release();
}