<?
/**
 * user.php
 * 
 * Allows a user to change settings.  Their control panel.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

if(isset($_POST['username'])){
	$updated = $lncln->user->updateUser($_POST);
}

require_once("includes/header.php");

if(isset($updated)){
	echo $updated;
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
			View Obscene: <select name="viewObscene">
							<option value=0 <?if($_COOKIE['obscene'] == 0) echo "selected";?>>No</option>
							<option value=1 <?if($_COOKIE['obscene'] == 1) echo "selected";?>>Yes</option>
						</select><br />
			<input type='submit' value="Login" />
		</div>
	</form>

<?
}
require_once("includes/footer.php");
?>