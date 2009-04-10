<?
/**
 * edit.php
 * 
 * Let's an admin edit a user
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if(!isset($_GET['user'])){
	echo "Please don't come here on your own.";
	include(ABSPATH . "includes/footer.php");
	exit();
}

$user = $lncln->getUser($_GET['user']);
?>

<form action="<?=createLink("edit", array("user" => $user['id']));?>" method="post">
	<div>
		Edit album:<br />
		<input type="hidden" name="id" value="<?=$user['id'];?>" />
		Name: <input type="text" name="name" value="<?=$user['name'];?>"/><br />
		Password: <input type="password" name="password" /><br />
		Password: <input type="password" name="confirm" /><br />
		Admin:  <select name="admin">
					<option value="0" <?if($user['admin'] == 0) echo "selected";?>>No</option>
					<option value="1" <?if($user['admin'] == 1) echo "selected";?>>Yes</option>
				</select><br />
		View Obscene: <input type='checkbox' name='viewObscene' <?if($user['obscene'] == 1) echo "checked";?> /><br />
		<input type="submit" value="Edit user"/>
	</div>
</form>