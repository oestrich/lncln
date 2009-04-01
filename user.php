<?
/**
 * user.php
 * 
 * Allows a user to change settings.  Their control panel.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

$lncln = new lncln();

if(isset($_POST['username'])){
	$updated = $lncln->updateUser($_POST);
}

require_once("includes/header.php");

if(isset($updated)){
	echo $updated;
}

if($lncln->isLoggedIn){
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
require_once("includes/footer.php");
?>