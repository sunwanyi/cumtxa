<!DOCTYPE html>
<html>
<head>
    <title>欢迎登陆后台</title>
    <base href="<?php echo $system['siteRoot'];?>plugin/cms/" />
    <meta charset="utf-8"/>
	<link href="style/loginForm.css" rel="stylesheet"/>
</head>
<body>
<h4>LikyhCms后台管理系统登陆</h4>
<form action="<?php echo $r['actionUrl'];?>" method="post">
    <fieldset>
        <legend>输入账号信息</legend>
        <label for="userInput">用户名</label>
        <input type="text" name="<?php echo $r['userTag'];?>" id="userInput" placeholder="请输入用户名"><br>
        <label for="passwordInput">密码：</label>
        <input type="password" name="<?php echo $r['passTag'];?>" id="passwordInput" placeholder="输入密码"><br>
		<input type="submit">
    </fieldset>
    
</form>
</body>
</html>