<?PHP
/**
* 文件处理统一接口类
*/
import("Lib.File.DirObject");
class FileObject {
    /**
     * 随机生成文件名
     * @param $ext 文件扩展名
     * @return string
     */
    public static function fileNameByRandom($ext=null){
        if(empty($ext)) $ext="tmp";
        $randomStr=getHash(rand(1,30000).time().$ext.rand(1,30000));
        return $randomStr.".".$ext;
    }
    /**
     * 使用时间生成文件名
     * @param $ext 文件扩展名
     * @return string
     */
    public static function fileNameByTime($ext=null){
        if(empty($ext)) $ext="tmp";
        return date("Hi_").rand(100000000, 1000000000).'.'.$ext;
    }

    /**
     * 获取文件扩展名
     * @param $filename
     * @return string
     */
    public static function fileExt($filename){
        $posOfDot=strripos($filename, '.');
        if($posOfDot!==false){
            return strtolower(substr($filename,$posOfDot+1,strlen($filename)));
        }
        return false;
    }

    /**
     * 读取一个文件
     * @param string $filename 文件名
     * @return string
     */
    public static function get($filename){
        return file_get_contents($filename);
    }

    /**
     * 对一个文件写入内容，返回写入信息，成功第一个量为success，第二个量为文件名，失败为错误信息
     * @param string $path 文件目录
     * @param string $filename 文件名
     * @param string $context 要写入的内容
     * @return array(state:bool, path:string, filename:string)
     */
    public static function createByContent($path, $filename, &$context){
        $path=DirObject::getFolder($path);
        if(is_writable($path)){
            $fp = fopen($path.$filename,"wb");
            if(fwrite($fp, $context)){
                return array("success",$path,$filename);
            }
        }
        return array("文件保存时出错",null,null);
    }

    /**
     * 保存一个上传来的文件
     * @param string $path 文件目录
     * @param string $filename 文件名
     * @param string $sourceFile 临时文件文件名
     * @return array(state:bool, path:string, filename:string) 返回格式参照createByContent
     */
    public static function createByTmp($path, $filename, $sourceFile){
        $path=DirObject::getFolder($path);
        if(is_writable($path)){
            if(move_uploaded_file($sourceFile, $path.$filename)){
                return array("success",$path,$filename);
            }
        }
        return array("文件保存时出错",null,null);
    }

    /**
     * 复制一个文件
     * @param string $path 文件目录
     * @param string $filename 文件名
     * @param string $sourceFile 源文件文件名
     * @return array(state:bool, path:string, filename:string) 返回格式参照createByContent
     */
    public static function createByCopy($path, $filename, $sourceFile){
        $path=DirObject::getFolder($path);
        if(is_writable($path)){
            if(copy($sourceFile, $path.$filename)){
                return array("success",$path,$filename);
            }
        }
        return array("文件保存时出错",null,null);
    }

    /**
     * 保存远程文件
     * @param string $path 文件目录
     * @param string $filename 文件名
     * @param string $sourceUrl 需要保存的远程文件url
     * @return array(state:bool, path:string, filename:string) 返回格式参照createByContent
     */
    public static function createByUrl($path, $filename, $sourceUrl){
        $path=DirObject::getFolder($path);
        if(is_writable($path)){
            $arrRequestHeaders = array(
                'http'=>array(
                    'method'        =>'GET',
                    'protocol_version'    =>1.1,
                    'follow_location'    =>1,
                    'header'=>    "User-Agent: LikyhPhp-0.20alpha-lts\r\n"
                )
            );
            $rc=copy($sourceUrl, $path.$filename, stream_context_create($arrRequestHeaders));
            if($rc&& fclose($rc)){
                return array("success",$path,$filename);
            }
        }
        return array("文件保存时出错",null,null);
    }

    /**
     * 对一个文件写入内容，文件名及子目录自动处理
     * @param string $path 文件目录
     * @param $ext 文件扩展名
     * @param string $context 要写入的内容
     * @return array(state:bool, filename:string) 返回格式参照createByContent
     */
    public static function autoCreateByContent($path, $ext=null, &$context){
        $path=DirObject::getFolderAppendDate($path);
        return self::createByContent($path, self::fileNameByTime($ext), $context);
    }

    /**
     * 保存一个上传来的文件，文件名及子目录自动处理
     * @param string $path 文件目录
     * @param $ext 文件扩展名
     * @param string $sourceFile 临时文件文件名
     * @return array(state:bool, filename:string) 返回格式参照createByContent
     */
    public static function autoCreateByTmp($path, $ext=null, $sourceFile){
        $path=DirObject::getFolderAppendDate($path);
        return self::createByTmp($path, self::fileNameByTime($ext), $sourceFile);
    }

    /**
     * 复制一个文件，文件名及子目录自动处理
     * @param string $path 文件目录
     * @param $ext 文件扩展名
     * @param string $sourceFile 源文件文件名
     * @return array(state:bool, filename:string) 返回格式参照createByContent
     */
    public static function autoCreateByCopy($path, $ext=null, $sourceFile){
        $path=DirObject::getFolderAppendDate($path);
        return self::createByCopy($path, self::fileNameByTime($ext), $sourceFile);
    }

    /**
     * 保存远程文件，文件名及子目录自动处理
     * @param string $path 文件目录
     * @param $ext 文件扩展名
     * @param string $sourceUrl 需要保存的远程文件url
     * @return array(state:bool, filename:string) 返回格式参照createByContent
     */
    public static function autoCreateByUrl($path, $ext=null, $sourceUrl){
        $path=DirObject::getFolderAppendDate($path);
        return self::createByUrl($path, self::fileNameByTime($ext), $sourceUrl);
    }
}