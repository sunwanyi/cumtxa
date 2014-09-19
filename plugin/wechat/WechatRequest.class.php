<?php
/**
 * WechatRequest 微信主动请求，如设置菜单，请求用户信息，下载上传媒体资料等
 * @author linyh
 */
class WechatRequest {
    const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    const AUTH_URL = '/token?grant_type=client_credential&';
    const MENU_CREATE_URL = '/menu/create?';
    const MENU_GET_URL = '/menu/get?';
    const MENU_DELETE_URL = '/menu/delete?';
    const MEDIA_GET_URL = '/media/get?';

    protected static $selfObject;
    protected $token;
    protected $appId;
    protected $appSecret;
    protected $accessToken;

    /**
     * 构造函数，检查配置是否完成
     */
    protected function __construct(){
        $this->token=getConfig("wechat", "token");
        $this->appId=getConfig("wechat", "appId");
        $this->appSecret=getConfig("wechat", "appSecret");
        $this->getAccessToken();
    }
    /**
     * 初始化函数，并返回当前类
     * @return WechatRequest
     */
    public static function init(){
        if(!self::$selfObject){
            self::$selfObject=new WechatRequest();
        }
        return self::$selfObject;
    }

    /**
     * GET 请求
     * @param string $url
     * @return bool|mixed
     */
    private function http_get($url){
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
     * POST 请求
     * @param string $url
     * @param array $param
     * @return string content
     */
    private function http_post($url,$param){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
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
     * 获取accessToken
     */
    protected function getAccessToken(){
        //TODO: get the cache access_token
        $result = $this->http_get(self::API_URL_PREFIX.self::AUTH_URL.'appid='.$this->appId.'&secret='.$this->appSecret);
        $json = json_decode($result,true);
        if (!$json || isset($json['errcode'])) {
            trigger_error("请求AccessKey失败：{$json['errcode']} {$json['errmsg']}",E_USER_ERROR);
            return;
        }
        $this->accessToken = $json['access_token'];
        // 过期时间
        // $expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
    }

    /**
     * 创建菜单
     * @param array $data 菜单数组数据
     * @return bool
     */
    public function createMenu($data){
        $result = $this->http_post(self::API_URL_PREFIX.self::MENU_CREATE_URL.'access_token='.$this->accessToken,self::json_encode($data));
        $json = json_decode($result,true);
        if (!$json || !empty($json['errcode'])) {
            trigger_error("请求菜单出错：{$json['errcode']} {$json['errmsg']}");
            return false;
        }
        return true;
    }

    /**
     * 获取菜单
     * @return array('menu'=>array(....s))
     */
    public function getMenu(){
        $result = $this->http_get(self::API_URL_PREFIX.self::MENU_GET_URL.'access_token='.$this->accessToken);
        $json = json_decode($result,true);
        if (!$json || isset($json['errcode'])) {
            trigger_error("请求菜单出错：{$json['errcode']} {$json['errmsg']}");
            return null;
        }
        return $json;
    }

    /**
     * 删除菜单
     * @return boolean
     */
    public function deleteMenu(){
        $result = $this->http_get(self::API_URL_PREFIX.self::MENU_DELETE_URL.'access_token='.$this->accessToken);
        $json = json_decode($result,true);
        if (!$json || !empty($json['errcode'])) {
            trigger_error("请求菜单出错：{$json['errcode']} {$json['errmsg']}");
            return false;
        }
        return true;
    }

    /**
     * 根据媒体文件ID获取媒体文件
     * @param string $media_id 媒体文件id
     * @return raw data
     */
    public function getMedia($media_id){
        $result = $this->http_get(self::API_URL_PREFIX.self::MEDIA_GET_URL.'access_token='.$this->accessToken.'&media_id='.$media_id);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                trigger_error("请求菜单出错：{$json['errcode']} {$json['errmsg']}");
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     * @return string
     */
    static function json_encode($arr) {
        if(!is_array($arr)){
            return (string)$arr;
        }
        $parts = array ();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys ( $arr );
        $max_length = count ( $arr ) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ( $arr as $key => $value ) {
            if (is_array ( $value )) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::json_encode ( $value ); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
            } else {
                $str = '';
                if (! $is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (is_numeric ( $value ) && $value<2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes ( $value ) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode ( ',', $parts );
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

    /**
     * 释放当前类
     */
    public static function release(){
        self::$selfObject=null;
    }
}