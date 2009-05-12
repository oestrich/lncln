<?
/**
 * edit.php
 * 
 * Let's an admin edit an album
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

if(!isset($_GET['album'])){
	$lncln->display->message("Please don't come here on your own.");
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