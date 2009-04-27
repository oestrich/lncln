<?
/**
 * edit.php
 * 
 * Let's an admin edit a group
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if(!isset($_GET['group'])){
	echo "Please don't come here on your own.";
	require_once(ABSPATH . "includes/footer.php");
	exit();
}

if(isset($_POST['name'])){
	$lncln->display->message($lncln->editGroup($_GET['group'], $_POST));
	
}

$group = $lncln->getGroup($_GET['group']);
?>

<form action="<?=createLink("edit", array("group" => $_GET['group']));?>" method="post" />
	<div>
		<table>
			<tr>
				<td>Name:</td>
				<td><input type="text" name="name" value="<?=$group['name'];?>" /></td>
			</tr>
			<tr>
				<td>Upload:</td>
				<td><?=$lncln->createSelect("upload", $group['upload']);?></td>
			</tr>
			<tr>
				<td>Directly to Index:</td>
				<td><?=$lncln->createSelect("index", $group['index']);?></td>
			</tr>
			<tr>
				<td title="Number of images that go directly to the homepage">Number to Index:</td>
				<td><input type="text" name="numIndex" size="3" value="<?=$group['numIndex'];?>" /></td>
			</tr>
			<tr>
				<td>Report:</td>
				<td><?=$lncln->createSelect("report", $group['report']);?></td>
			</tr>
			<tr>
				<td>Report Value:</td>
				<td><input type="text" name="report" size="3" value="<?=$group['reportValue'];?>" /></td>
			</tr>
			<tr>
				<td>Rate:</td>
				<td><?=$lncln->createSelect("rate", $group['rate']);?></td>
			</tr>
			<tr>
				<td>Rate Value:</td>
				<td><input type="text" name="rate" size="3" value="<?=$group['rateValue'];?>" /></td>
			</tr>
			<tr>
				<td>Use obscene:</td>
				<td><?=$lncln->createSelect("obscene", $group['obscene']);?></td>
			</tr>
			<tr>
				<td>Use refresh:</td>
				<td><?=$lncln->createSelect("refresh", $group['refresh']);?></td>
			</tr>
			<tr>
				<td>Use delete:</td>
				<td><?=$lncln->createSelect("delete", $group['delete']);?></td>
			</tr>
			<tr>
				<td>Use caption:</td>
				<td><?=$lncln->createSelect("caption", $group['caption']);?></td>
			</tr>
			<tr>
				<td>Use tag:</td>
				<td><?=$lncln->createSelect("tag", $group['tag']);?></td>
			</tr>
			<tr>
				<td>Manage albums:</td>
				<td><?=$lncln->createSelect("album", $group['album']);?></td>
			</tr>
		</table>
		<input type="submit" value="Submit" />
	</div>
</form>