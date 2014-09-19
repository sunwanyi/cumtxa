<?php
/**
 * 路由器，所有实现了这一个接口的类都可以
 * @author linyh
 */
interface RouterInterface extends RunnableInterface {
    /**
     * 在exec执行这个路由并分析出指向的类以后调用
     * 返回router解析出来的类对应的对象
     * @return class
     */
    public function getTransformClass();
}