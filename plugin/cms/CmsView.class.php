<?php
import('Lib.Core.Widget');
import('Lib.Data.SimpleFile');
/**
 * CmsView 用于显示cms
 * @author linyh
 */
class CmsView {
    const TYPE_FORM=1;
    const TYPE_TABLE=2;
    const TYPE_ITEM_LIST=4;
    const TYPE_STATE=8;
    const TYPE_JQUERY=16;
    const TYPE_DATETIME=32;
    const TYPE_EDITOR=64;
    const TYPE_MAP=128;
    // cms配置信息
    protected $config;
    // 一系列功能的标题（对应url的page）
    protected $pageTitle;
    // 一个页面的标题（对应url的action）
    protected $actionTitle;
    // 右上角用户名称
    protected $userName;

    protected static $selfObject;
    protected function __construct() {
        $this->config=getConfig('cms',null);
        $this->config['controlFile']=null;
    }

    /**
     * 初始化函数
     * @param string $pageTitle page标题（一系列页面标题）
     * @param string $controlFile 控制文件
     * @param array $config
     * @return CmsView
     */
    public static function init($pageTitle=null, $controlFile=null, $config=null){
        if(!isset(self::$selfObject)){
            self::$selfObject=new CmsView();
        }
        $cmsView=self::$selfObject;
        if($cmsView instanceof CmsView){
            if(!empty($pageTitle)){
                $cmsView->setPageTitle($pageTitle);
            }
            if(!empty($controlFile)){
                $cmsView->setControlFile($controlFile);
            }
            if(!empty($config)){
                $cmsView->loadConfig($config);
            }
        }
        return $cmsView;
    }

    public function loadConfig($config){
        if(is_array($config)){
            $this->config=array_merge($this->config,$config);
        }
    }

    // 设置各种属性
    public function setPageTitle($pageTitle) {
        $this->pageTitle = $pageTitle;
    }
    public function setActionTitle($actionTitle) {
        $this->actionTitle = $actionTitle;
    }
    public function setUserName($userName) {
        $this->userName = $userName;
    }
    public function setControlFile($filename){
        $this->config['controlFile']=$filename;
    }

    public function loginScene($actionUrl, $userTag='user',$passTag='pass'){
        $result=array(
            'actionUrl'=>$actionUrl,
            'userTag'=>$userTag,
            'passTag'=>$passTag
        );
        View::displayAsHtml($result,'plugin/cms/loginForm.php');
    }

    public function normalScene($result, $tpl, $cmsOption){
        $result['pageTitle']=$this->pageTitle;
        $result['actionTitle']=$this->actionTitle;
        $result['logoCopyright']=$this->config['logoCopyright'];

        $result['component']=(int)$cmsOption;

        $cnw=new CmsNavWidget($this->config['navFile']);
        $result['navHtml']=$cnw->__toString();

        if(isset($this->config['controlFile'])&&!empty($this->config['controlFile'])){
            $cnw=new CmsControlWidget($this->config['controlFile']);
            $result['controlHtml']=$cnw->__toString();
        }else{
            $result['controlHtml']='';
        }

        if(isset($this->config['userInfoFile'])&&!empty($this->config['userInfoFile'])){
            $cuw=new CmsUserInfoWidget($this->config['userInfoFile']);
            $cuw->setName($this->userName);
            $result['userInfoHtml']=$cuw->__toString();
        }
        $result['sourceTpl']=$tpl;
        View::displayAsHtml($result, "plugin/cms/cmsTpl.php");
    }

    public function itemListScene($text){
        $result['text']=$text;
        $this->normalScene($result, null, self::TYPE_ITEM_LIST);
    }
    public function formScene($result, $tpl){
        $this->normalScene($result, $tpl, self::TYPE_FORM);
    }
    public function tableScene($result, $tpl){
        $this->normalScene($result, $tpl, self::TYPE_TABLE);
    }
}
abstract class CmsWidget extends Widget {
    protected $control;
    protected $iconPath="plugin/cms/images/nav/";
    protected $jsonData;
    protected $cacheHtml;
    function __construct($filename) {
        $this->jsonData=json(SimpleFile::read("./site/".$filename));
    }
    protected function urlFormat($array){
        $webRouter=getAppInfo()->webRouter;
        // 生成url
        if(isset($array['absolute'])){
            $url=$webRouter->getAbsolute($array['absolute']);
        }else if(isset($array['url'])){
            $url=$array['url'];
        }else{
            $array+=array("set"=>null,"page"=>null,"action"=>null,"qus"=>null);
            $url=$webRouter->getURL($array['set'],$array['page'],$array['action'],$array['qus']);
        }
        return $url;
    }
    protected function itemFormat(&$array){
        foreach($array as &$v){
            if(isset($v['icon'])){
                $iconNumber=(int)$v['icon'];
                $v['icon']=$this->iconPath."{$iconNumber}.png";

                // 生成子菜单
                if(isset($v['item'])&&is_array($v['item'])&& !empty($v['item'])){
                    $this->itemFormat($v['item']);
                }
            }
            $v['url']=$this->urlFormat($v);
        }
        return $array;
    }
}
class CmsNavWidget extends CmsWidget{
    protected $iconPath="plugin/cms/images/nav/";
    function cal(){
        $nav=$this->jsonData;
        $result['title']=$nav['title'];
        $result['item']=$this->itemFormat($nav['item']);
        return $this->cacheHtml=View::displayAsHtml($result,"plugin/cms/nav.php", false);
    }
    function __toString() {
        return $this->cacheHtml?:$this->cal();
    }
}
class CmsControlWidget extends CmsWidget{
    protected $iconPath="plugin/cms/images/control/";
    function cal(){
        $info=$this->jsonData;
        $result['title']=$info['title'];
        $result['item']=$this->itemFormat($info['item']);
        return $this->cacheHtml=View::displayAsHtml($result,"plugin/cms/control.php", false);
    }
    function __toString() {
        return $this->cacheHtml?:$this->cal();
    }
}
class CmsUserInfoWidget extends CmsWidget{
    protected $name;
    public function setName($name) {
        $this->name = $name;
    }
    function cal(){
        $info=$this->jsonData;
        $name=$this->name?:$info['defaultName'];
        $result['userTips']=sprintf($info['tips'],$name);
        $result['item']=$this->itemFormat($info['item']);
        return $this->cacheHtml=View::displayAsHtml($result,"plugin/cms/userinfo.php", false);
    }
    function __toString() {
        return $this->cacheHtml?:$this->cal();
    }
}