<?php
/**
 * web请求分析接口
 * @author linyh
 */
interface WebRouterInterface extends RouterInterface{

    /**
     * 获取$_GET 数组
     * 可以是索引数组或者关联数组
     * 也可以是同时索引数组和者关联数组
     * @return array
     */
    public function getGet();

    /**
     * 获取含有post的数组
     * @return array
     */
    public function getPost();
}