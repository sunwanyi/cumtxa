<?php
/**
* 日志类
* @author linyh，sg
*/
import('FileLoader');
import("PathGeneration");
class ErrorLog{
	// 必须保证打开唯一
	private static $FileLoader;
    private static $selfObject;

    // 日期和时间 Y-m-d H:i:s
	private $dtime;
    // 日期 Ymd
	private $day;

	/**
	* 构造函数，初始化配置
	*/
	private function __construct(){
		if(!empty(self::$FileLoader)) return;
		
		$this->day=date('Ymd',time());
		$this->dtime=date('Y-m-d H:i:s',time());
		self::$FileLoader= new FileLoader(
            PathGeneration::getFolder(".\\log\\")."log_xqx_{$this->day}.log", null);

	}

    /**
     * 初始化操作
     * @return ErrorLog
     */
    static public function init(){
        if(!isset(self::$selfObject)){
            self::$selfObject= new ErrorLog();
        }
        return self::$selfObject;
    }

    /**
     * 在日志写入信息
     * @param string $level 信息重要级别（log,warning,error）
     * @param string $type 要写入的信息，标记类型
     * @param string $message 要写入的信息，具体信息
     * @return int 成功写入的字符个数，写入失败返回0，没有操作权限返回-1
     */
    public function record($level, $type, $message){
        if(self::$FileLoader->isWritable()){
            $value="[{$level} type:{$type} time:{$this->dtime}] {$message} \n";
            // var_dump($value);
            $r=self::$FileLoader->write($value);
            if($r){
                return $r;
            }else{
                return 0;
            }
        }else{
            return -1;
        }
    }

    /**
     * log的别名
     */
    public function L($type, $message){
        trigger_error("ErrorLog->L() 此方法不推荐系统函数使用");
        return $this->log($type, $message);
    }
	
	/**
	* 在日志写入普通信息
	* @param string $type 要写入的信息，标记类型
    * @param string $message 要写入的信息，具体信息
	* @return int 成功写入的字符个数，写入失败返回0，没有操作权限返回-1
	*/
	public function log($type, $message){
		return $this->record("log", $type, $message);
	}

    /**
     * 要写入的警告
     * @param string $type 类型
     * @param string $message 具体信息
     * @return int 成功写入的字符个数，写入失败返回0，没有操作权限返回-1
     */
    public function warning($type, $message){
        return $this->record("warning", $type, $message);
    }

    /**
     * 要写入的警告
     * @param string $type 类型
     * @param string $message 具体信息
     * @return int 成功写入的字符个数，写入失败返回0，没有操作权限返回-1
     */
    public function error($type, $message){
        return $this->record("error", $type, $message);
    }
}