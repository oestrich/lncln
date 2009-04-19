<?
/**
 * edit.php
 * 
 * Let's an admin edit a user
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if(!isset($_GET['user'])){
	echo "Please don't come here on your own.";
	include(ABSPATH . "includes/footer.php");
	exit();
}

if(isset($_POST['id'])){
	$lncln->changeUser($_POST);
	header("location:" . createLink("manage"));
	exit();
}

$user = $lncln->getUser($_GET['user']);
?>

<form action="<?=createLink("edit", array("user" => $user['id']));?>" method="post">
	<div>
		Edit User: <?=$user['name'];?><br />
		<input type="hidden" name="id" value=<?=$user['id'];?> />
		<table>
			<tr>
				<td>Password:</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type="password" name="confirm" /></td>
			</tr>
			<tr>
				<td colspan="2">Leave password blank to keep the same.</td>
			</tr>
			<tr>
				<td>Admin:</td>
				<td><select name="admin">
					<option value=0 <?if($user['admin'] == 0) echo "selected";?>>No</option>
					<option value=1 <?if($user['admin'] == 1) echo "selected";?>>Yes</option>
				</select></td>
			</tr>
			<tr>
				<td>View Obscene:</td>
				<td><select name="viewObscene">
							<option value=0 <?if($user['obscene'] == 0) echo "selected";?>>No</option>
							<option value=1 <?if($user['obscene'] == 1) echo "selected";?>>Yes</option>
						</select></td>
			</tr>
		</table>
		<input type="submit" value="Edit user"/>
	</div>
</form>