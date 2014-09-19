<?php
import('Request');
/**
 * 从聚合获取数据
 * Class JuheWeatherMode
 */
class JuheWeatherMode extends Data {
    protected $appkey;

    protected function onStart() {
        $this->appkey=getConfig("juhe","appkey");
    }

    public function getBase($city){
        $url=getConfig("juhe","index");
        $param=array(
            "cityname"=>$city,
            "dtype" => "json",
            "key" => $this->appkey
        );
        $dataStr=Request::get($url, $param);
        if($dataStr!==false){
            return json_decode($dataStr,true);
        }else{
            return false;
        }
    }

    public function getUni(){
        $url=getConfig("juhe","uni");
        $param=array(
            "dtype" => "json",
            "key" => $this->appkey
        );
        $dataStr=Request::get($url, $param);
        if($dataStr!==false){
            return json_decode($dataStr,true);
        }else{
            return false;
        }
    }

    public function getForecast3h($city){
        $url=getConfig("juhe","forecast3h");
        $param=array(
            "cityname"=>$city,
            "dtype" => "json",
            "key" => $this->appkey
        );
        $dataStr=Request::get($url, $param);
        if($dataStr!==false){
            return json_decode($dataStr,true);
        }else{
            return false;
        }
    }
} 