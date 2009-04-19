<?
/**
 * edit.php
 * 
 * Let's an admin edit an album
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if(!isset($_GET['album'])){
	echo "Please don't come here on your own.";
	include(ABSPATH . "includes/footer.php");
	exit();
}

if(isset($_POST['name'])){
	$lncln->changeAlbumName($_POST['id'], $_POST['name']);
	header("location:" . createLink("manage"));
	exit();
}

$album = $lncln->getAlbum($_GET['album']);

?>

<form action="<?=createLink("edit", array("album" => $album['id']));?>" method="post">
	<div>
		Edit album:<br />
		<input type="hidden" name="id" value="<?=$album['id'];?>" />
		<input type="text" name="name" value="<?=$album['name'];?>"/>
		<input type="submit" value="Edit album"/>
	</div>
</form>