<div class="contentTitle">
    <h2><?php echo $r['pageTitle'];?></h2>
        <span>当前位置：
            <a href="javascript:">后台首页</a>
            <?php if(!empty($r['pageTitle'])){ ?><a href="javascript:"><?php echo $r['pageTitle'];?></a><?php } ?>
            <?php if(!empty($r['actionTitle'])){ ?><a href="javascript:"><?php echo $r['actionTitle'];?></a><?php } ?>
        </span>
</div>