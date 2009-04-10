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
		<input type="text" name="name" value="<?=$user['name'];?>"/>
		<input type="submit" value="Edit album"/>
	</div>
</form>