<?php
/**
 * 只有数字字母下划线验证，也就是是否符合变量名
 * @param string $content 要过滤的字符串
 * @return string 返回是否符合，符合为true
 */
function validateVar($content) {
    return preg_match("/^[A-Za-z_][A-Za-z0-9_]*$/", $content);
}

function json($s){
    return json_decode($s,true);
}