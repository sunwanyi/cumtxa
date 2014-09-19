<?php
/**
 * 所有Activity接口
 * @author linyh
 */
interface ActivityInterface extends RunnableInterface{
    /**
     * 执行该名称对应的方法
     * @param $action string 要执行的方法
     * @return bool 是否成功运行
     */
    public function devolve($action);
}