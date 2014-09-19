<?php
/**
 * 用curl发送一个http请求
 * Class Request
 */
class Request {
    /**
     * @param string $url 请求的参数
     * @param array|string $param 请求的参数，可以是数组或者字符串
     * @return bool|mixed
     */
    public static function get($url, $param){
        if(!empty($param)){
            $url.= stripos($url,"?")===FALSE?"?":"&";
            $url .=is_array($param)?self::paramImplode($param):$param;
        }
        return self::http_get($url);
    }

    /**
     * 发送post请求
     * @param string $url
     * @param array|string $getParam get的参数，可以是数组或者字符串，下同
     * @param array|string $postParam post的参数
     * @return string
     */
    public static function post($url, $getParam, $postParam){
        if(!empty($getParam)){
            $url.= stripos($url,"?")===FALSE?"?":"&";
            $url .=is_array($getParam)?self::paramImplode($getParam):$getParam;
        }
        $postStr =!empty($postParam)&&is_array($postParam)?self::paramImplode($postParam):$postParam;
        return self::http_post($url,$postStr);
    }

    private static function paramImplode($param){
        return implode("&",array_map(
            create_function('$key, $value', 'return urlencode($key)."=".urlencode($value);'),
            array_keys($param), array_values($param)));
    }

    /**
     * GET 请求
     * @param string $url
     * @return bool|mixed
     */
    public static function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * POST 请求，请求字符串不允许包括换行
     * @param string $url
     * @param string $postStr post主题内容
     * @return string content
     */
    public static function http_post($url,$postStr){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $postStr = str_replace(array("/r/n", "/r", "/n"), "", $postStr);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$postStr);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }
}