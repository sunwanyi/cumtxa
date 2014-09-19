<?php
/**
 * path构造类，新建文件路径
 * @author linyh
 */
class DirObject {
	/**
	 * 将路径转换为小写
	 * @param $path string 表示要检查的路径
	 * @return string
	 */
    protected static function format($path) {
		return strtolower(strtr($path, '\\', '/'));
	}
	
	/**
	 * 在路径后面加上斜杠
	 * @param $path string 表示要检查的路径
	 * @return string
	 */
	protected static function appendSlash($path) {
		$path= self::format($path);
        if ( strrchr( $path , "/" ) != "/" ) {
            $path .= "/";
        }
        return $path;
	}
	
	/**
	 * 根据$url检查路径，如果不存在则添加
	 * @param $path string 表示要检查的路径
	 * @return string
	 */
	public static function getFolder($path) {
		$path=self::appendSlash($path);
		if( !file_exists($path)){
			if( !mkdir($path, 0777, true)){
				return null;
			}
		}
		return $path;
	}

	/**
	 * 根据$url检查路径，并且自动按照日期分类到不同的文件夹中
	 * @param $path string 表示要检查的路径
	 * @return string
	 */
	public static function getFolderAppendDate($path) {
        $path = self::appendSlash($path) . date( "Ymd" ). "/";
		return self::getFolder($path);
	}

    /**
     * 根据$url检查路径，并且自动按照日期分类到不同的文件夹中
     * @param $path string 表示要检查的路径
     * @param $value 要增加的子文件夹
     * @return string
     */
    public static function getFolderAppendDateAndValue($path, $value) {
        if(!$value) $value='default';
        $path = self::appendSlash($path) . date( "Ymd" ). "/$value/";
        return self::getFolder($path);
    }
}