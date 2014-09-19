<?PHP
/**
* 文件读取写入类
* @author linyh，sg
*/
class FileLoader {
	/**
	 * 默认配置
	 */
	private $config=array(
			"mode" => 'append'//可以是read,write或者append，表示读取、重新写或者追加
		);
    // TODO 使用flock改进这一个类

	private $filename;
	private $file;//文件句柄
	public $if_open=false;
	
	/**
	* 构造函数，初始化配置
	*/
	public function __construct($filename, $config) {
		//载入配置
		if(isset($config)){
			foreach($config as $key=>$i){
				$this->config[$key]=$i;
			}
		}
		//设置要操作的文件名
		$this->filename=$filename;
// 		var_dump($filename);
		//打开要操作的文件
		switch ($this->config['mode']) {
			case 'read':
				if(is_readable($this->filename)){
					$this->file=fopen($this->filename, 'r');
				}
				break;
			case 'write':
				if(!is_file($this->filename) ||
					is_file($this->filename) && is_writable($this->filename)){
					$this->file=fopen($this->filename, 'w');
				}
				break;
			case 'append':
			default:
				if(!is_file($this->filename) ||
					is_file($this->filename) && is_writable($this->filename)){
					$this->file=fopen($this->filename, 'a');
				}
		}
		// 若打开成功就把打开标记修改
		if($this->file){
			$this->if_open=true;
		}
	}

	/**
	 * 检测是否可以读取
	 */
	public function isReadable(){
		return $this->if_open&&$this->config['mode']=='read';
	}

	/**
	 * 检测是否可以写入
	 */
	public function isWritable(){
		// var_dump($this->if_open);
		// var_dump($this->config['mode']);
		return $this->if_open && $this->config['mode']!='read';
	}

	/**
	* 读取所有行
	* @return array 读取出的内容
	*/
	public function getFileArray(){
		if($this->isReadable()){
			 //把文件以数组形式放入$fileArr中
			while(!feof($this->file)){
				$eachLine=fgets($this->file);
				$fileArr[]=$eachLine;
			}	
			return $fileArr;				
		}
		else return array();
	}

	/**
	* 读取所有行
	* @return array 读取出的内容
	*/
	public function getFile(){
		echo "该方法要修改 via Lib.File.FileLoader line 94;";
		if($this->isReadable()){
			 //把文件以数组形式放入$fileArr中
			while(!feof($this->file)){
				$eachLine=fgets($this->file);
				$fileArr[]=$eachLine;
			}	
			return $fileArr;				
		}
		else return array();
	}

	/**
	* 读取文件，合成一个字符串
	* @return string 读取出的内容
	*/
	public function getFileString(){
		if($this->isReadable()){
			// 把文件以字符串形式放入$file中
			$file="";
			while(!feof($this->file)){
				$eachLine=fgets($this->file);
// 				var_dump($eachLine);
				$file.=$eachLine;
			}
			return $file;				
		}else{
			return '';
		}
	}

	/**
	* 写入数据
	* @param string[] $s 要写入的数据
	* @return int 成功写入的字符个数，写入失败返回0，没有操作权限返回-1
	*/
	public function write(){
		if($this->isWritable()){
			$s=null;
			$num=func_num_args();  //返回调用函数的传入参数个数,类型是整型
			$value=func_get_args();  //返回全部参数的值,类型是数组
			for($i=0; $i<$num; $i++){
				$s .=$value[$i];
			}
			$write = fwrite($this->file,$s);
			if($write){
				return strlen($s);
			}else{
				return 0;
			}	
			
		}else{
			return -1;
		}		
	}
	
	public function close(){
		return fclose($this->file);
	}
}