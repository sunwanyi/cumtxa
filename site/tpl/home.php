<!DOCTYPE html>
<html lang="cn">
	<head>
		<meta charset="utf-8" />
        <base href="<?php echo $system['siteRoot'];?>" />

        <title>框架测试</title>
		<meta name="description" content="测试 likyhPHP likyh团队" />
	</head>

	<body>
		<div>
			<header>
				<h1>欢迎来到likyhPHP测试兼演示界面</h1>
			</header>
			
			<div>
                <table>
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>名字</th>
                        <th>学号</th>
                        <th>宿舍</th>
                        <th>生日</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($result['student'] as $v){ ?>
                    <tr>
                        <td><?php echo $v['id']; ?></td>
                        <td><?= $v['name']; ?></td>
                        <td><?= $v['num']; ?></td>
                        <td><?= $v['dormitory']; ?></td>
                        <td><?php echo $v['birthday'];?></td>
                    </tr>
                    <?php } ?>
                    </tbody>

                </table>
			</div>

			<footer>
			</footer>
		</div>
	</body>
</html>