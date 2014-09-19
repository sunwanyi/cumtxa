<?php
/**
 * Wechat Router
 * @author linyh
 */
import("SimpleSession");
import("CallbackStack");
class WechatRouter implements WebRouterInterface {
    const MESSAGE_TYPE_TEXT = 'text';
    const MESSAGE_TYPE_IMAGE = 'image';
    const MESSAGE_TYPE_LOCATION = 'location';
    const MESSAGE_TYPE_LINK = 'link';
    const MESSAGE_TYPE_EVENT = 'event';
    const MESSAGE_TYPE_MUSIC = 'music';
    const MESSAGE_TYPE_VOICE = 'voice';
    const MESSAGE_TYPE_VIDEO = 'video';

    const MESSAGE_EVENT_SUBSCRIBE = 'subscribe';
    const MESSAGE_EVENT_UNSUBSCRIBE = 'unsubscribe';
    const MESSAGE_EVENT_SCAN = 'SCAN';
    const MESSAGE_EVENT_LOCATION = 'LOCATION';
    const MESSAGE_EVENT_CLICK = 'CLICK';

    protected static $selfObject;
    /**
     * @var CallbackStack
     */
    public $callbackStack;
    protected $activity;

    protected $appInfo;
    protected $token;
    protected $postArray;

    /**
     * 解析数组，记录着怎么解析，暂时还没有用
     * @var array
     */
    protected $decodeArray;

    /**
     * 初始化函数，并返回当前类
     * @return WechatRouter
     */
    public static function init(){
        if(!self::$selfObject){
            self::$selfObject=new WechatRouter();
        }
        return self::$selfObject;
    }

    /**
     * 构造函数，检查配置是否完成
     */
    protected function __construct(){
        SimpleSession::init(null, getHash($_POST['FromUserName']));
        if(!$_SESSION['wechatActivity']) $_SESSION['wechatActivity']=null;
        if(!$_SESSION['callbackStack']) $_SESSION['callbackStack']=null;
        $this->activity=&$_SESSION['wechatActivity'];
        $this->callbackStack=&$_SESSION['callbackStack'];

        $this->token=getConfig("wechat", "token");
        $this->appInfo=&getAppInfo();
    }

    /**
     * For weixin server validation
     */
    protected function checkSignature(){
        $signature = isset($_GET["signature"])?$_GET["signature"]:'';
        $timestamp = isset($_GET["timestamp"])?$_GET["timestamp"]:'';
        $nonce = isset($_GET["nonce"])?$_GET["nonce"]:'';

        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        return $tmpStr == $signature;
    }

    /**
     * 执行这个路由并分析出指向的类
     */
    public function exec(){
        $postStr = file_get_contents("php://input");
        $this->postArray = !empty($postStr)?(array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA):array();
        if(!$this->checkSignature()){
            trigger_error("验证不通过！",E_USER_ERROR);
        }
    }

    /**
     * 重置activity
     */
    public function resetWechatActivity(){
        $this->activity=null;
        $this->callbackStack=new CallbackStack();
    }

    public function getGet(){
        return $_GET;
    }

    public function getPost(){
        if(is_array($this->postArray)){
            return $this->postArray;
        }else{
            return array();
        }
    }

    public function getTransformClass() {
        if(!$this->checkSignature()){
            trigger_error("验证不通过！",E_USER_ERROR);
        }
        if($_GET['echostr']){
            echo $_GET['echostr'];
            return null;
        }

        // 建立调用栈
        // WechatRouter做了限制，点击菜单相当于查找Activity，发送消息相当于选择action
        $post=$this->postArray;
        if($post['MsgType']==self::MESSAGE_TYPE_EVENT){
            // 如果是点击事件，则更改选中的activity
            $folderKey=$post['Event']!=self::MESSAGE_EVENT_CLICK?$post['Event']:$post['EventKey'];
            $this->callbackStack=new CallbackStack();
            $this->activity=$folderKey;
            $this->appInfo->action=strtolower($post['Event']);
            $classSpacePath=getConfig("router",$folderKey);
        }else{
            // 如果是普通消息，则检查是否有保存上一次的信息
            $classSpacePath=getConfig("router",$this->activity?:'default');
            $this->appInfo->action=$this->callbackStack->getAction()?:$this->postArray['MsgType'];
        }

        $page=array_pop(explode(".",$classSpacePath));
        if(import($classSpacePath)){
            return $page::init($this->appInfo);
        }else{
            trigger_error("未找到该对象",E_USER_ERROR);
            return null;
        }
    }

    /**
     * 释放当前类
     */
    public static function release(){
        self::$selfObject=null;
    }
}