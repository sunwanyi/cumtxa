<?php
import('Request');
/**
 * 从聚合获取数据
 * Class JuheSMSMode
 */
class JuheSMSMode extends Data {
    protected $appkey;

    protected function onStart() {
        $this->appkey=getConfig("juhe","appkey");
    }

    /**
     * 发送短信
     * @param string|array $mobile 要发送的人的手机号码，可以是字符串或者一个数组
     * @param string $tplName 从juhe.config.ini中获取，id需要事先在聚合网站上设置好
     * @param array $param 要填充的变量
     * @return array 发送结果
     */
    public function send($mobile, $tplName, $param){
        $mobile=(array)$mobile;
        $url=getConfig("juhe","send");
        $tplId=getConfig("juhe",$tplName);
        $paramStr=implode("&",array_map(
            create_function('$key, $value', 'return urlencode($key)."=".urlencode($value);'),
            array_keys($param), array_values($param)));
        foreach($mobile as $v){
            $dataStr=Request::get($url,array(
                "mobile"=>$v,
                "tpl_id"=>$tplId,
                "tpl_value"=>$paramStr,
                "dtype" => "json",
                "key"=>$this->appkey
            ));
            $result[]=json_decode($dataStr, true);
        }
        return $result;
    }
} 