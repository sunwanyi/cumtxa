<div id="userControl">
    <ul class="button">
        <?php foreach($r['item'] as $k => $i){ ?>
            <li><a href="<?php echo $i['url'];?>"><?php echo $i['name']; ?></a></li>
        <?php } ?>
    </ul>
    <div class="message"><?php echo $r['userTips'];?></div>
</div>