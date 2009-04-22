<?
/**
 * iconActions.php
 * 
 * This file adds actions to a page that handles the icons underneath images
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 


if(isset($_GET['thumb'])){
	$extra = "&thumb=true";
}

$extra .= str_replace("&amp;", "&", $lncln->extra);

$scriptLocation = $lncln->script == "image.php" ? $scriptLocation = URL . $lncln->script . "?img=" . $_GET['img'] . $extra : URL . $lncln->script . "?page=" . $_GET['page'] . $extra;

if($_GET['post'] == true){
	$lncln->upload();
	header("location:". URL . "index.php");
	exit();
}

if(isset($_GET['delete']) && $lncln->user->permissions['delete'] == 1){
	$deletion = $lncln->delete($_GET['delete']);
	header("location:" . $scriptLocation . "#" . $_GET['delete']);
	exit();
}

if(isset($_GET['obscene']) && $lncln->user->permissions['obscene'] == 1){
	$obscene = $lncln->obscene($_GET['obscene']);
	header("location:" . $scriptLocation . "#" . $_GET['obscene']);
	exit();
}

if(isset($_GET['rateUp']) && $lncln->user->isUser){
	//This should probably be handled by the function itself
	$lncln->rate($_GET['rateUp'], $lncln->user->permissions['rate']);
	header("location:" . $scriptLocation . "#" . $_GET['rateUp']);
	exit();
}

if(isset($_GET['rateDown']) && $lncln->user->isUser){	
	//This should probably be handled by the function itself
	$lncln->rate($_GET['rateDown'], -1 * $lncln->user->permissions['rate']);
	header("location:" . $scriptLocation . "#" . $_GET['rateDown']);
	exit();
}

if($_GET['viewObscene']){
	if($_COOKIE['obscene'] == false || !$_COOKIE['obscene']){
		setcookie('obscene', true, time() + (60 * 60 * 24));
	}
	else{
		setcookie('obscene', true, time() - (60 * 60 * 24));
	}
	header("location:". URL . "index.php");	
	exit();
}

if(isset($_GET['refresh']) && $lncln->user->permissions['refresh'] == 1){
	$id = prepareSQL($_GET['refresh']);
	
	$sql = "SELECT type FROM images WHERE id = " . $id;
	$result = mysql_query($sql);
	
	if(mysql_num_rows($result) == 1){
		$row = mysql_fetch_assoc($result);
		$lncln->thumbnail($id . "." . $row['type']);
	}
	header("location:" . $scriptLocation . "#" . $_GET['refresh']);
	exit();
}

if($_GET['caption'] && $lncln->user->permissions['caption'] == 1){
	$lncln->caption($_POST['id'], $_POST['caption']);
	header("location:" . $scriptLocation . "#" . $_POST['id']);
	exit();
}

if($_GET['tag'] && $lncln->user->permissions['tag'] == 1){
	$lncln->tag($_POST['id'], $_POST['tags']);
	header("location:" . $scriptLocation . "#" . $_POST['id']);
	exit();
}

if($_GET['action'] == "album" && $lncln->user->permissions['album'] == 1){
	$lncln->changeAlbum($_POST['id'], $_POST['album']);
	header("location:" . $scriptLocation . "#" . $_POST['id']);
	exit();
}
?>
