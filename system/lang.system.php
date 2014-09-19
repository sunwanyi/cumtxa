<?php
/**
 * 定义系统函数
 * @author linyh
 */

function __autoload($className){
    import($className);
}

/**
 * 导入组件，比如import("Lib.File.Image");
 * @author linyh
 * @param $className
 * @return bool 是否成功导入
 */
function import($className){
    static $cacheClass;
    if(strstr($className,'.')===false){
        $className=getConfig("classHash",$className);
    }
    // 检查是否导入过
    if(isset($cacheClass[$className])){
        return true;
    }

    $sEach=explode(".",$className);

    // 检查是否符合要求
    foreach($sEach as $v){
        if(!validateVar($v)){
            trigger_error("需要导入的路径{$v}不符合变量名定义",E_USER_ERROR);
        }
    }

    // 最后一个点之后的内容大小写不变，其他的都变成小写
    $componentPath='';
    for($i=0;$i<sizeof($sEach)-1;$i++){
        $componentPath.=strtolower($sEach[$i]).'/';
    }
    $componentPath.=$sEach[$i].".class.php";

    // 按照物理路径导入
    if(require $componentPath){
        $cacheClass[$className]=true;
        return true;
    }else{
        trigger_error("需要导入的类{$className}未找到:$componentPath",E_USER_WARNING);
        return false;
    }
}



/**
 * 获取该名称的配置信息
 * @param $configSet string 配置集合名称
 * @param $configName string|null 配置条目名称
 * @return string|array
 */
function getConfig($configSet, $configName=null, $process_sections=false){
    $config=&$GLOBALS['config'];
    if(!isset($config[$configSet])){
        $config[$configSet]=analysisIniFile("config/{$configSet}.config.ini",$process_sections);
    }
    assert(!empty($config[$configSet]));
    if(empty($configName)){
        return $config[$configSet];
    }else{
        assert(isset($config[$configSet][$configName]));
        return $config[$configSet][$configName];
    }
}

/**
 * 分析Ini文件
 * @param string $path 文件地址
 * @param bool $process_sections
 * @return array 分析结果，二维数组
 */
function analysisIniFile($path,$process_sections=true){
    if(is_readable($path)){
        return parse_ini_file($path, $process_sections);
    }else{
        trigger_error("找不到指定的配置文件:{$path}",E_USER_WARNING);
        return array();
    }
}