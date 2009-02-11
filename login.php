<?
/*
/	lncln by Eric Oestrich
/	Version .5
/
*/

require_once('config.php');
require_once('functions.php');

connect($config['mysql']);

if(isset($_POST['username']) || (!isset($_COOKIE['password']) && isset($_COOKIE['username']))){
	if(!isset($_COOKIE['password']) && !isset($_POST['username'])){
		setcookie("username", $username, time() - 30);
		header("location:login.php");
	}
	
	if(isset($_POST['username'])){
		$username = stripslashes($_POST['username']);
		$password = stripslashes($_POST['password']);
	}
	if(isset($_COOKIE['password'])){
		$username = stripslashes($_POST['username']);
		$password = stripslashes($_POST['password']);
	}
	
	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string($password);

	if(isset($_POST['username'])){
		$password = sha1($password);
	}

	$sql = "SELECT * FROM users WHERE name = '" . $username . "' AND password = '" . $password . "'";
	
	$result = mysql_query($sql);
	$numRows = mysql_num_rows($result);
	
	if($numRows == 1){
		$row = mysql_fetch_assoc($result);
		setcookie("username", $username, time() + (60 * 60 * 24));
		setcookie("password", $password, time() + (60 * 60 * 24));
		setcookie('obscene', $row['obscene'], time() + (60 * 60 * 24));
		$isLoggedIn = true;
	}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title><?echo $config['title'];?> Login</title>
	<link type="text/css" rel="stylesheet" href="style.css" />
<?
	if($isLoggedIn){
?>
	<meta http-equiv="refresh" content="2;url=index.php">
<?
	}
?>
	<style type='text/css'>
		body{
			text-align: center;
		}
		form{
			margin: auto;
			margin-top: 50px;
		}
	</style>
</head>
<body onload="document.getElementById('username').focus();">
	<h1 id="header">
		<? echo $config['title'];?>
	</h1>
	<?
	if(!$numRows && !$_COOKIE['username']){?>
	<form enctype="multipart/form-data" action="login.php" method="post">
		<div>
			Username: <input type='text' name='username' id='username'/><br />
			Password: <input type='password' name='password' /><br />
			<input type='submit' value="Login" />
		</div>
	</form>
	<?}
	else{?>
	<div id='loggedIn'>
		Welcome <?if(isset($_COOKIE['username'])){echo $_COOKIE['username'];}else{echo $_POST['username'];}?>!<br />
		Go back to the <a href="index.php">main page</a>
	</div>
	<?}?>
</body>
</html>