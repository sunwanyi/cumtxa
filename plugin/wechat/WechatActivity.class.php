<?php
import('Plugin.Wechat.WechatView');
import('Plugin.Wechat.WechatRequest');
import('Plugin.Wechat.WechatRouter');
class WechatActivity extends Activity {
    /**
     * @var WechatView
     */
    protected $wechatView;
    /**
     * @var WechatRouter
     */
    protected $wechatRouter;
    protected function onStart(){
        $this->wechatView=WechatView::init();
        $this->wechatRouter=WechatRouter::init();
    }

}