<?
/**
 * lncln by Eric Oestrich
 * version 0.6.0
 * 
 * @package lncln
 */

require_once("config.php");
require_once("functions.php");

connect($config['mysql']);
list($isLoggedIn, $isAdmin) = loggedIn();

if(isset($_POST['username'])){
	$updated = updateUser($_POST);
}

require_once("header.php");

if(isset($updated)){
	echo $updated;
}

if($isLoggedIn){
	$sql = "SELECT obscene FROM users WHERE name = '" . $_COOKIE['username'] . "' LIMIT 1";
	$result = mysql_query($sql);
	
	$row = mysql_fetch_assoc($result);
	if($row['obscene']){
		$checked = "checked";
	}
	else{
		$checked = "";
	}
?>
<form enctype="multipart/form-data" action="user.php" method="post">
	<div id="user">
		<input type="hidden" name="username" value="<?echo $_COOKIE['username'];?>" />
		Only put in your password if you want to change it.<br />
		Old Password: <input type='password' name='password' /><br />
		New Password: <input type='password' name='newPassword' /><br />
		New Password: <input type='password' name='newPasswordConfirm' /><br />
		<br />
		View Obscene: <input type='checkbox' name='viewObscene' <?echo $checked;?>/><br />
		<input type='submit' value="Login" />
	</div>
</form>

<?
}
require_once("footer.php");
?>