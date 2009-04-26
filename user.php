<?
/**
 * user.php
 * 
 * Allows a user to change settings.  Their control panel.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

include_once(ABSPATH . "includes/header.php");

if(isset($_POST['username'])){
	echo $lncln->user->updateUser($_POST);
	echo "<br />Click <a href='" . URL . "index.php'>here</a> to continue";
	include_once(ABSPATH . "includes/footer.php");
	exit();
}

if($lncln->user->isUser){
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
			View Obscene: <select name="obscene">
							<option value=0 <?if($_COOKIE['obscene'] == 0) echo "selected";?>>No</option>
							<option value=1 <?if($_COOKIE['obscene'] == 1) echo "selected";?>>Yes</option>
						</select><br />
			<input type='submit' value="Login" />
		</div>
	</form>

<?
}
include_once(ABSPATH . "includes/footer.php");
?>