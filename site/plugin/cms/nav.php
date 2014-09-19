<nav>
    <h2><?php echo $r['title'];?></h2>
    <?php foreach($r['item'] as $i){ ?>
    <div class="menuItem">
        <div class="title">
            <a href="<?php echo $i['url']; ?>">
                <img src="<?php echo $i['icon']; ?>">
                <?php echo $i['name']; ?>
            </a>
        </div>
    </div>
    <?php } ?>
</nav>