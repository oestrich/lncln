<?
/**
 * login.php
 * 
 * Does exactly what it seems like it should
 *  
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

require_once("load.php");

if(isset($_POST['username']) || (!isset($_COOKIE['password']) && isset($_COOKIE['username']))){
	if(!isset($_COOKIE['password']) && !isset($_POST['username'])){
		setcookie("username", "", time() - 30, URL);
		header("location:login.php");
	}
	
	if(isset($_POST['username'])){
		$username = addslashes($_POST['username']);
		$password = addslashes($_POST['password']);
	}
	if(isset($_COOKIE['password'])){
		$username = addslashes($_POST['username']);
		$password = addslashes($_POST['password']);
	}
	
	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string($password);

	if(isset($_POST['username'])){
		$password = sha1($password);
	}
	
	$sql = "SELECT obscene FROM users WHERE name = '" . $username . "' AND password = '" . $password . "'";
	$result = mysql_query($sql);
	$numRows = mysql_num_rows($result);
	
	$row = mysql_fetch_assoc($result);
	
	if($numRows == 1){
		$obscene = $row['obscene'] == 1 ? true : false;

		setcookie("username", $username, time() + (60 * 60 * 24), URL);
		setcookie("password", $password, time() + (60 * 60 * 24), URL);
		setcookie("obscene", $obscene, time() + (60 * 60 * 24), URL);
		$isLoggedIn = true;
	}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title><?echo $lncln->display->settings['title'];?> Login</title>
	<link type="text/css" rel="stylesheet" href="<?echo URL;?>theme/<?echo THEME;?>/style.css" />
<?
	if($isLoggedIn){
?>
	<meta http-equiv="refresh" content="2;url=<?=URL;?>index/">
<?
	}
?>
	<style type='text/css'>
		form{
			margin: auto;
			margin-top: 50px;
		}
		#title{
			text-align: center;
			font-size: 40px;
			font-weight: bold;
			position: relative;
			left: -40px;
		}
	</style>
</head>
<body onload="document.getElementById('username').focus();">
	<div id="container">
		<div id="header">
			<a href="<?echo URL;?>index/" ><img src="<?echo URL;?>theme/<?echo THEME;?>/images/abe.png" alt="Abe" id="abeLink" /></a>
			<div id="title"><?echo $lncln->display->settings['title'];?></div>
		</div>
		<div id="mainBody">
<?
			if(!$numRows && !$_COOKIE['username']){
?>
			<form enctype="multipart/form-data" action="<?=URL;?>login" method="post">
				<div>
					Username: <input type='text' name='username' id='username'/><br />
					Password: <input type='password' name='password' />
					<input type='submit' value="Login" />
				</div>
			</form>
<?
			}
			else{
?>
			<div id='loggedIn'>
				Welcome <?if(isset($_COOKIE['username'])){echo $_COOKIE['username'];}else{echo $_POST['username'];}?>!<br />
				Go back to the <a href="index.php">main page</a>
			</div>
<?
			}
?>
		</div>
	</div>
</body>
</html>