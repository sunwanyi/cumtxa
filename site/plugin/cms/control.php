<div id="contentControl">
    <?php foreach($r['item'] as $k => $i){ ?>
        <a href="<?php echo $i['url'];?>" class="button control" id="<?php echo $k;?>ContentBtn">
            <img src="<?php echo $i['icon']; ?>">
            <?php echo $i['name']; ?>
        </a>
    <?php } ?>
</div>