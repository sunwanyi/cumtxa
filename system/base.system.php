<?php
/**
 * 定义系统函数
 * @author linyh
 */

/**
 * 执行likyh可运行的Runnable对象
 * @param $object
 * @param $action
 */
function execRunnable($object, $action){
    if($object instanceof RunnableInterface){
//        $activity=$object::init($GLOBALS['globalAppInfo']);
        $activity=$object;
        $object->exec($action);
        $object->release();
    }
}

function import_part($classFullName, $action){
    import($classFullName);
    if($s=strrchr($classFullName, ".")){
        $className=substr($s, 1);
    }else{
        $className=$classFullName;
    }
    $object=$className::init($GLOBALS['globalAppInfo']);
    execRunnable($object, $action);
}

/**
 * @return AppInfo
 */
function &getAppInfo(){
    return $GLOBALS['globalAppInfo'];
}