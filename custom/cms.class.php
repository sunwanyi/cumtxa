<?php
import('CmsView');
class cms extends Activity {
    /** @var CmsView */
    protected $cms;
    protected function __construct() {
        parent::__construct();
        $this->cms=CmsView::init("文章管理");
        $this->cms->setPageTitle("文章管理");
        $this->cms->setUserName("可爱的依然");
        $this->cms->setControlFile("tpl/admin/control.json");
    }

    function indexTask(){
        echo "这是首页，没有的页面也回到这里";
    }

    function loginTask(){
        $this->cms->loginScene("表单提交地址");
    }

    function formTask(){
        $this->cms->setActionTitle("修改文章");
        $this->cms->formScene(array(),"tpl/admin/form.php");
    }

    function tableTask(){
        $this->cms->setActionTitle("查看文章");
        $this->cms->tableScene(array(),"tpl/admin/table.php");
    }

    function richTask(){
        $this->cms->setActionTitle("查看插件");
        $this->cms->normalScene(array(),"tpl/admin/form.php",
            CmsView::TYPE_FORM| CmsView::TYPE_JQUERY| CmsView::TYPE_EDITOR);
    }
}