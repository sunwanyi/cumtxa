<?PHP
/**
* 新的文件读取写入类
* @author linyh，sg
*/
import("PathGeneration");
class SimpleFile {
    /**
     * 读取一个文件
     * @param string $filename 文件名
     * @return string
     */
    public static function read($filename){
        return file_get_contents($filename);
    }

    /**
     * 对一个文件写入内容，返回写入的字符数，失败则返回-1
     * @param $path 文件目录
     * @param $filename 文件名
     * @param $context 要写入的内容
     * @param null $length 自定义一个允许写入的最大长度
     * @return int|null
     */
    public static function write($path, $filename, &$context, $length=null){
        $path=PathGeneration::getFolder($path);
        if(is_writable($path.$filename)){
            $fp = fopen($path.$filename,"wb");
            if(fwrite($fp, $context, $length)){
                return empty($length)?strlen($context):$length;
            }
        }
        return -1;
    }

    public static function append($path, $filename, &$context, $length=null){
        $path=PathGeneration::getFolder($path);
        if(is_writable($path.$filename)){
            $fp = fopen($path.$filename,"ab");
            if(fwrite($fp, $context, $length)){
                return empty($length)?strlen($context):$length;
            }
        }
        return -1;
    }

}