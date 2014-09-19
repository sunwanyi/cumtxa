<?php
/**
 * @author linyh
 */
class TextFilter {
	/**
	 * 显示到屏幕上的文本的字符过滤
	 * @param string $content 要过滤的字符串
	 * @return string 返回替换后的字符串
	 */
	public static function htmlReplace($content) {
		$content = str_replace(
				array('"',"'","\\","/","<",">",""),
				array("&quot;","&quot;","、","、","&lt;","&gt;","&nbsp;"),
				trim($content));
		return $content;
	}
	/**
	 * 纯中文的文本的字符过滤
	 * @param string $content 要过滤的字符串
	 * @return string 返回替换后的字符串
	 */
	public static function htmlReplaceByChinese($content) {
		$content = str_replace(
				array('"',"'","\\","/","<",">"," "),
				array("＂","＇","、","、","《","》","　"),
				trim($content));
		return $content;
	}
    public static function htmlCut($content,$start, $length = null, $encoding = 'utf-8'){
        $content=htmlentities(strip_tags($content),ENT_NOQUOTES,"utf-8");
        return mb_substr($content, $start, $length, $encoding);
    }
	/**
	 * 用户名等纯字符文本的过滤
	 * @param string $content 要过滤的字符串
	 * @return string 返回替换后的字符串
	 */
	public static function pureReplace($content) {
		$content = str_replace(
				array('"',"'","\\","/","<",">"),
				array("","","","","",""),
				trim($content));
		return $content;
	}
	
	/**
	 * 只有数字字母下划线过滤
	 * @param string $content 要过滤的字符串
	 * @return string 返回替换后的字符串
	 */
	public static function varReplace($content) {
		//TODO
		return $content;
	}
	
	/**
	 * 只有数字字母下划线验证
	 * @param string $content 要过滤的字符串
	 * @return string 返回是否符合，符合为true
	 */
	public static function varValidate($content) {
		return preg_match("/^[A-Za-z_][A-Za-z0-9_]*$/", $content);
	}
}