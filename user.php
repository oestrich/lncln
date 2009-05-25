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

if(isset($_POST['username'])){
	$lncln->display->message($lncln->user->updateUser($_POST) . "<br />Click <a href='" . URL . "index.php'>here</a> to continue");
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
	<form enctype="multipart/form-data" action="<?=URL;?>user/" method="post">
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
