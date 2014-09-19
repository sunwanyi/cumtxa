<?php

interface HTMLAnalysisInterface {
    /**
     * 新建、初始化并返回当前类
     * @return \HTMLAnalysisInterface
     */
    public static function init();

    /**
     * 设置url目录
     * @param $folderArray
     * @return bool
     */
    public function setFolder($folderArray);

    public function setGet($getArray);

    /**
     * 释放当前类
     * @return bool
     */
    public static function release();

    /**
     * 显示内容
     * @param &$result
     * @param $disPath
     * @return boolean 是否执行成功
     */
    public function display(&$result,$disPath);
}