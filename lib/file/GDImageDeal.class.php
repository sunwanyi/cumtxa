<?php
/*像这样使用
import("Lib.File.GDImageDeal");
$a=GDImageDeal::readImageByFilename('F:\workspace\www\1.png');
$b=$a->crop(400,400);
$b->saveImage('F:\workspace\www\2.png');
*/
/**
 * 图片裁剪类
 * 强制裁剪，生成图片严格按照需要，不足放大，超过裁剪，图片始终铺满
 * 默认jpg补白为白色，png补白成透明像素，
 * 使用SaveImage()方法保存图片
 * @author linyh
 */
class GDImageDeal {
    /**
     * @var GdImage
     */
    protected $image;

    /**
     * @param GdImage $image
     */
    function __construct($image) {
        $this->image=$image;
	}

    /**
     * 使用文件名初始化当前类
     * @param $filename
     * @return \GDImageDeal|null
     */
    public static function readImageByFilename($filename){
        $type=exif_imagetype($filename);
        $image=null;
        switch($type){
            case IMAGETYPE_JPEG :$image=GdImageJpeg::initByFileName($filename);break;
            case IMAGETYPE_PNG :$image=GdImagePng::initByFileName($filename);break;
            case IMAGETYPE_GIF :$image=GdImageGif::initByFileName($filename);break;
            default :
        }
        $imageDeal=new GDImageDeal($image);
        return $imageDeal;
    }

    /**
     * 裁剪函数，两个参数表示需要的长宽，可以为百分比，表示目标大小为现在的百分之多少
     * @param number $width
     * @param number $height
     * @return GdImage
     */
	public function crop($width, $height) {
        $sourceWidth=$this->image->getWidth();
        $sourceHeight=$this->image->getHeight();

        if($width<1) $width=$sourceWidth * $width;
        if($height<1) $height=$sourceHeight * $height;

        $sourceHWRatio=$sourceHeight / $sourceWidth;
        $targetHWRatio=$height / $width;
        if($sourceHWRatio > $targetHWRatio){
            $ratio=1.0 * $width / $sourceWidth;
        }else{
            $ratio=1.0 * $height / $sourceHeight;
        }
        $tmp_w=(int)($width/$ratio);
        $tmp_h=(int)($height/$ratio);
        $tmp_x=(int)($sourceWidth-$tmp_w)/2;
        $tmp_y=(int)($sourceHeight-$tmp_h)/2;
        $targetImage = $this->image->initNew($width, $height);
        imagecopyresampled($targetImage->getImage(), $this->image->getImage(),
            0, 0, $tmp_x, $tmp_y,
            $width, $height, $tmp_w, $tmp_h);
        return $targetImage;
    }
}
abstract class GdImage {
    protected $image;
    function  __construct($image){
        if(is_resource($image)){
            $this->image = $image;
        }else{
            trigger_error("构造函数所需的值不是一个图片。",E_USER_WARNING);
        }
    }
    abstract function getImage();
    abstract function saveImage($filename);
    abstract function outImage();
    function getWidth(){
        return imagesx($this->image);
    }
    function getHeight(){
        return imagesy($this->image);
    }
}
class GdImageJpeg extends GdImage{
    static function initByFileName($filename){
        return new GdImageJpeg(imagecreatefromjpeg($filename));
    }
    static function initNew($width, $height){
        return new GdImageJpeg(imagecreatetruecolor($width, $height));
    }
    function getImage(){
        return $this->image;
    }
    function saveImage($filename){
        imagejpeg($this->image, $filename, 100);
    }
    function outImage(){
        header('Content-type: image/jpeg');
        imagejpeg($this->image);
    }
}
class GdImageGif extends GdImage{
    static function initByFileName($filename){
        return new GdImageGif(imagecreatefromgif($filename));
    }
    static function initNew($width, $height){
        $targetImage = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
        imagefill($targetImage, 0, 0, $bg);
        imagecolortransparent($targetImage, $bg);
        return new GdImageGif($targetImage);
    }
    function getImage(){
        return $this->image;
    }
    function saveImage($filename){
        imagegif($this->image, $filename);
    }
    function outImage(){
        header('Content-type: image/jpeg');
        imagegif($this->image);
    }
}
class GdImagePng extends GdImage{
    static function initByFileName($filename){
        return new GdImagePng(imagecreatefrompng($filename));
    }
    static function initNew($width, $height){
        $image = imagecreatetruecolor ($width, $height);
        imagesavealpha($image,true);
        imagealphablending($image, false);
        return new GdImagePng($image);
    }
    function getImage(){
        return $this->image;
    }
    function saveImage($filename){
        imagepng($this->image, $filename);
    }
    function outImage(){
        header('Content-type: image/jpeg');
        imagepng($this->image);
    }
}
?>