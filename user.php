<?
/**
 * user.php
 * 
 * Allows a user to change settings.  Their control panel.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
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
			<table>
				<tr>
					<td colspan="2">Only put in your password if you want to change it.</td>
				</tr>
				<tr>
					<td>Old Password:</td>
					<td><input type='password' name='password' /></td>
				</tr>
				<tr>
					<td>New Password:</td>
					<td><input type='password' name='newPassword' /></td>
				</tr>
				<tr>
					<td>New Password:</td>
					<td><input type='password' name='newPasswordConfirm' /></td>
				</td>
				<tr>
					<td>View Obscene:</td>
					<td>
						<select name="obscene">
							<option value=0 <?if($_COOKIE['obscene'] == 0) echo "selected";?>>No</option>
							<option value=1 <?if($_COOKIE['obscene'] == 1) echo "selected";?>>Yes</option>
						</select>
					</td>
				</tr>
			</table>
			<input type='submit' value="Login" />
		</div>
	</form>

<?
}
else{
	$lncln->display->message("Please sign in first.");
}
include_once(ABSPATH . "includes/footer.php");
?>