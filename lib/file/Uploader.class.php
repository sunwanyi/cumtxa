<?php
/**
 * 通用上传类
 * @author linyh
 */
import("FileObject");
import("DirObject");
class Uploader {
    protected $config;               //配置信息
    protected $fileName;
    protected $stateMap = array(    //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS" ,                //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制" ,
        "文件大小超出 MAX_FILE_SIZE 限制" ,
        "文件未被完整上传" ,
        "没有文件被上传" ,
        "上传文件为空" ,
        "POST" => "文件大小超出 post_max_size 限制" ,
        "SIZE" => "文件大小超出网站限制" ,
        "TYPE" => "不允许的文件类型" ,
        "DIR" => "目录创建失败" ,
        "IO" => "输入输出错误" ,
        "UNKNOWN" => "未知错误" ,
        "MOVE" => "文件保存时出错"
    );

    protected static $selfObject;
    /**
     * 初始化函数，并返回当前类
     * @return Uploader
     */
    public static function init(){
        if(!self::$selfObject){
            self::$selfObject=new Uploader();
        }
        return self::$selfObject;
    }
    protected function __construct(){
        $this->config = getConfig("uploader");
        $this->config['allowFiles']=explode(",", $this->config['defaultAllow']);
    }

    /**
     * 修改上传的配置，包括保存地址，允许的文件类型，允许的最大大小
     * @param string $savePath 保存的路径，存放从[upload][rootPath]目录开始的文件夹
     */
    public function changeConfig($savePath=null){
        if(!empty($savePath)){
            $this->config['savePath']=PathGeneration::getFolder($this->config['rootPath'].$savePath);
        }
    }

    /**
     * 检测文件是否允许上传，失败返回类型(字符串)，如果成功则返回0
     * @param array $info 文件信息
     * @param null $allowFiles 允许的文件类型，描述性的字符串就可以，具体的在配置文件中
     * @param null $maxSize 大小限制，单位kb
     * @return int|string
     */
    protected function checkFile(&$info, $allowFiles=null, $maxSize=null){
        if (!$info){ return 'UNKNOWN'; }
        // 检查上传错误
        if ($info['error']){ return $info['error'];}
        // 检查是否为上传文件
        if (!is_uploaded_file($info['tmp_name'])){ return "UNKNOWN"; }
        // 检测文件大小
        if(!$maxSize) $maxSize=$this->config['maxSize'];
        if ($info['size'] > ($maxSize * 1024)){ return "SIZE"; }
        // 检测文件类型
        $allowFilesArray=!empty($allowFiles)? explode(",", $this->config[$allowFiles]): $this->config['allowFiles'];
        $info["ext"] = FileObject::fileExt($info["name"]);
        if (!in_array($info["ext"], $allowFilesArray)){ return "TYPE"; }
        return 0;
    }

    /**
     * 上传文件的主处理方法
     * @param string $fileField 表单名称
     * @param string $savePath 保存的路径，存放从[upload][rootPath]目录开始的文件夹
     * @param string $allowFiles 允许的文件类型，描述性的字符串就可以，具体的在配置文件中
     * @param int $maxSize 大小限制，单位kb
     * @return array($stateInfo, $f) 返回一个成功信息和一个详细的内容(上传文件)
     */
    public function upFile($fileField, $savePath=null, $allowFiles=null, $maxSize=null){
        $this->changeConfig($savePath);
        // 检查文件是否可以上传
        $info=isset($_FILES[$fileField])? $_FILES[$fileField]: null;
        $ifChecked=$this->checkFile($info, $allowFiles, $maxSize);
        $stateInfo=$this->getStateInfo($ifChecked);

        if($ifChecked===0){
            list($state, $info['savePath'], $info['filename'])=FileObject::autoCreateByTmp($this->config['savePath'], $info['ext'], $info['tmp_name']);
            if (strtolower($state)=="success"){
                $info['url']=getConfig("site", "root"). $info['savePath'] .$info['filename'];
            }else{
                $stateInfo = $this->getStateInfo( "MOVE" );
            }
        }
        unset($info['tmp_name']);
        return array($stateInfo, $info);
    }

    /**
     * 上传一张图片并保存为缩略图（丢弃原图）
     * @param string $fileField 表单名称
     * @param string $savePath 保存的路径，存放从[upload][rootPath]目录开始的文件夹
     * @param array $thumbArray 要生成的缩略图的信息，使用二维数组：[{'set':'big_image','width':135,'height':246}, ...]
     * @return array($stateInfo, $f) 返回一个成功信息和一个详细的内容(素材文件)
     */
    public function upImage($fileField, $savePath=null, $thumbArray){
        $this->changeConfig($savePath);
        // 检查文件是否可以上传
        $info = isset($_FILES[$fileField])? $_FILES[$fileField]: null;
        $ifChecked=$this->checkFile($info);
        $stateInfo=$this->getStateInfo($ifChecked);

        if($ifChecked===0){
            // 读取文件，开始生成压缩图
            import("GDImageDeal");
            $filename=$info["tmp_name"];
            if(is_readable($filename)){
                $gdd=GDImageDeal::readImageByFilename($filename);
                foreach($thumbArray as $value){
                    $targetImage=$gdd->crop($value['width'], $value['height']);
                    $filePath = PathGeneration::getFolderAppendDateAndValue($this->config['savePath'], $value['set']);
                    $fileName= FileObject::fileNameByTime($info['ext']);
                    $targetImage->saveImage($filePath.$fileName);
                    //$targetImage->saveImage("cache/0.tmp");
                    //list($1,$2,$3)=FileObject::autoCreateByCopy($this->config['savePath'], $info['ext'], $info['tmp_name']);

                    // 纪录成功信息
                    $tf['width']=$value['width'];
                    $tf['height']=$value['height'];
                    $tf['savePath']=$filePath;
                    $tf['filename']=$fileName;
                    $tf['url']=getConfig("site", "root"). $filePath. $fileName;
                    $info[]=$tf;
                }
            }
        }
        unset($info['tmp_name']);
        return array($stateInfo, $info);
    }

    /**
     * 通过错误code获取错误提示
     * @param $errCode
     * @return string
     */
    private function getStateInfo( $errCode ) {
        return !$this->stateMap[ $errCode ] ? $this->stateMap[ "UNKNOWN" ] : $this->stateMap[ $errCode ];
    }
}