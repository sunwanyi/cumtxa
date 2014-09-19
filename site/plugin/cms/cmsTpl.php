<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?php echo $system['siteRoot'];?>" />
    <script>
        root='<?php echo $system['root'];?>';
    </script>
    <title><?php echo $r['pageTitle'];?> - LikyhCMS管理系统</title>
    <link href="plugin/cms/style/common.css" rel="stylesheet"/>
    <?php if($r['component']&CmsView::TYPE_FORM){ ?>
        <link href="plugin/cms/style/form.css" rel="stylesheet"/>
    <?php } ?>
    <?php if($r['component']&CmsView::TYPE_TABLE){ ?>
        <link href="plugin/cms/style/table.css" rel="stylesheet"/>
    <?php } ?>
    <?php if($r['component']&CmsView::TYPE_ITEM_LIST){ ?>

    <?php } ?>
    <?php if($r['component']&CmsView::TYPE_STATE){ ?>

    <?php } ?>
    <?php if($r['component']&CmsView::TYPE_JQUERY){ ?>
        <script src="plugin/jquery-1.10.2.min.js" type="text/javascript"></script>
    <?php } ?>
    <?php if($r['component']&CmsView::TYPE_DATETIME){ ?>

    <?php } ?>
    <?php if($r['component']&CmsView::TYPE_EDITOR){ ?>
        <script>
            ueditorController='<?php e_url("admin","ueditor","index");?>';
        </script>
        <script src="plugin/ueditor/ueditor.config.js" type="text/javascript" charset="utf-8"></script>
        <script src="plugin/ueditor/ueditor.all.js" type="text/javascript" charset="utf-8"> </script>
        <script>
            $(document).ready(function(){
                $(".editor").each(function(){
                    var ue = UE.getEditor($(this).attr("id"));
                    ue.ready(function() {
                        var html = ue.getContent();
                        ue.execCommand('pasteplain');
                    });
                });

            })
        </script>
    <?php } ?>
    <?php if($r['component']&CmsView::TYPE_MAP){ ?>
        <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=HNtF8PpKdoLMQTDurEuZUVTE"></script>
        <script type="text/javascript">
            var map = new BMap.Map("baeMap");
            var myGeo = new BMap.Geocoder();
            map.enableScrollWheelZoom();    //启用滚轮放大缩小
            map.enableContinuousZoom();    //启用地图惯性拖拽
            map.centerAndZoom("北京",12);
        </script>
    <?php } ?>
</head>
<body>
<div id="container">
    <header>
        <div id="company">
            <div class="title <?php echo $r['logoCopyright']?"show":"hidden";?>"><span>@LikyhCMS</span></div>
        </div>
        <div id="logo">LikyhCMS管理系统</div>
        <?php echo $r['userInfoHtml']; ?>
    </header>
    <div id="main">
        <?php echo $r['navHtml']; ?>
        <div id="content">
            <?php import_tpl('plugin/cms/contentTitle.php'); ?>
            <?php echo $r['controlHtml']; ?>
            <div id="data">
                <?php import_tpl($r['sourceTpl']);?>
            </div>
        </div>
    </div>
    <?php import_tpl('plugin/cms/footer.php'); ?>
</div>
</body>
</html>