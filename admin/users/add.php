<?
/**
 * add.php
 * 
 * Let's an admin create a user, what else?
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
 
if(isset($_POST['username'])){
	echo $lncln->adduser($_POST);
}

?>
<form enctype="multipart/form-data" action="<?=createLink("add");?>" method="post">
	<div id="adduser">
		<table>
			<tr>
				<td>Username:</td>
				<td><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type='password' name='password' /></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type='password' name='passwordconfirm' /></td>
			</tr>
			<tr>
				<td>Admin:</td>
				<td><select name="admin">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select></td>
			</tr>
			<tr>
				<td>Group:</td>
				<td><?=$lncln->listGroups();?></td>
			</tr>
		</table>
		<input type="submit" value="Add user" />
	</div>
</form>