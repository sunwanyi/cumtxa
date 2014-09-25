<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 14-9-25
 * Time: 下午2:16
 */
import("custom.data.adminMode");
class user extends Activity{
    /** @var CmsView  */
    protected $cms;
    /** @var  adminMode */
    protected $user;
    protected function __construct() {
        parent::__construct();
        $this->cms=CmsView::init();
        $this->user=adminMode::init();
    }
    function loginTask(){
        $web=WebRouter::init();
        $this->cms->loginScene($web->getAction("loginSubmit"));
    }

    function loginSubmitTask(){
        if(!isset($_POST['user'])||empty($_POST['user'])||!isset($_POST['pass'])){
            echo "信息不完整！";
            return ;
        }
        $result=$this->user->login($_POST['user'],$_POST['pass']);
        if($result==0){
            echo "登陆失败！";
        }

    }
} 