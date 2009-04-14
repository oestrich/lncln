<?
/**
 * add.php
 * 
 * Let's an admin create a user, what else?
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
 
if(isset($_POST['username'])){
	$added = $lncln->adduser($_POST);
}

if(isset($added)){
	echo $added;
}
?>
<form enctype="multipart/form-data" action="<?=createLink("add");?>" method="post">
		<div id="adduser">
		Username: <input type="text" name="username" /><br />
		Password: <input type='password' name='password' /><br />
		Password: <input type='password' name='passwordconfirm' /><br />
		Admin: <select name="admin">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>
		<input type="submit" value="Add user" />
	</div>
</form>