<?php
// 运行框架核心内容

/**
 * appinfo除了记载post，get等信息，还会记载当前运行的类的加载信息
 * @var AppInfo
 */
$globalAppInfo=new AppInfo();
$router=WebRouter::init($globalAppInfo);
$globalAppInfo->webRouter=$router;
while($router instanceof RouterInterface){
    $router->exec();
    if($router instanceof WebRouterInterface){
        $_GET=$router->getGet();
        $_POST=$router->getPost();
    }
    // 并解析下一个对象
    $router=$router->getTransformClass();
}
// 运行分析出的Runnable对象
$object=$router;
if($object instanceof RunnableInterface){
    $object->exec($globalAppInfo->action);
    $object->release();
}
