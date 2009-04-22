<?
/**
 * edit.php
 * 
 * Let's an admin edit a group
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if(!isset($_GET['group'])){
	echo "Please don't come here on your own.";
	include(ABSPATH . "includes/footer.php");
	exit();
}

$group = $lncln->getGroup($_GET['group']);
?>

<form action="<?=createLink("add");?>" method="post" />
	<div>
		<table>
			<tr>
				<td>Name:</td>
				<td><input type="text" name="name" value="<?=$group['name'];?>" /></td>
			</tr>
			<tr>
				<td>Directly to Index:</td>
				<td><select name="index" ><option value="0">No</option><option value="1">Yes</option></select></td>
			</tr>
			<tr>
				<td title="Number of images that go directly to the homepage">Number to Index:</td>
				<td><input type="text" name="numIndex" size="3" value="<?=$group['numImages'];?>" /></td>
			</tr>
			<tr>
				<td>Report Value:</td>
				<td><input type="text" name="report" size="3" value="<?=$group['report'];?>" /></td>
			</tr>
			<tr>
				<td>Rate Value:</td>
				<td><input type="text" name="rate" size="3" value="<?=$group['rate'];?>" /></td>
			</tr>
			<tr>
				<td>Use obscene:</td>
				<td><?createSelect("obscene", $group['obscene']);?></td>
			</tr>
			<tr>
				<td>Use refresh:</td>
				<td><?createSelect("refresh", $group['refresh']);?></td>
			</tr>
			<tr>
				<td>Use delete:</td>
				<td><?createSelect("delete", $group['delete']);?></td>
			</tr>
			<tr>
				<td>Use caption:</td>
				<td><?createSelect("caption", $group['caption']);?></td>
			</tr>
			<tr>
				<td>Use tag:</td>
				<td><?createSelect("tag", $group['tag']);?></td>
			</tr>
			<tr>
				<td>Manage albums:</td>
				<td><?createSelect("album", $group['album']);?></td>
			</tr>
		</table>
		<input type="submit" value="Submit" />
	</div>
</form>