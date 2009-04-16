<?
/**
 * iconActions.php
 * 
 * This file adds actions to a page that handles the icons underneath images
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 


if(isset($_GET['thumb'])){
	$extra = "&thumb=true";
}

$extra .= str_replace("&amp;", "&", $lncln->extra);

$scriptLocation = URL . $lncln->script . "?page=" . $_GET['page'] . $extra;

if($lncln->script == "image.php"){
	$scriptLocation = URL . $lncln->script . "?img=" . $_GET['img'] . $extra;
}

if($_GET['post'] == true){
	$lncln->upload();
	header("location:". URL . "index.php");
	exit();
}

if(isset($_GET['delete']) && $lncln->user->permissions['isAdmin'] == 1){
	$deletion = $lncln->delete($_GET['delete']);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra . "#" . $_GET['delete']);
	exit();
}

if(isset($_GET['obscene']) && $lncln->user->isUser){
	$obscene = $lncln->obscene($_GET['obscene']);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra . "#" . $_GET['obscene']);
	exit();
}

if(isset($_GET['rateUp']) && $lncln->user->isUser){
	//This should probably be handled by the function itself
	$rating = 1;
	if($lncln->user->permissions['isAdmin'] == 1){
		$rating = 5;
	}
	$lncln->rate($_GET['rateUp'], $rating);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra . "#" . $_GET['rateUp']);
	exit();
}

if(isset($_GET['rateDown']) && $lncln->user->isUser){	
	//This should probably be handled by the function itself
	$rating = -1;
	if($lncln->user->permissions['isAdmin'] == 1){
		$rating = -5;
	}
	$lncln->rate($_GET['rateDown'], $rating);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra . "#" . $_GET['rateDown']);
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

if(isset($_GET['refresh']) && $lncln->user->isUser){
	$id = stripslashes($_GET['refresh']);
	$id = mysql_real_escape_string($id);
	
	$sql = "SELECT type FROM images WHERE id = " . $id;
	$result = mysql_query($sql);
	
	if(mysql_num_rows($result) == 1){
		$row = mysql_fetch_assoc($result);
		$lncln->thumbnail($id . "." . $row['type']);
	}
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra . "#" . $_GET['refresh']);
	exit();
}

if($_GET['caption'] && $lncln->user->isUser){
	$lncln->caption($_POST['id'], $_POST['caption']);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra . "#" . $_POST['id']);
	exit();
}

if($_GET['tag'] && $lncln->user->isUser){
	$lncln->tag($_POST['id'], $_POST['tags']);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra . "#" . $_POST['id']);
	exit();
}

if($_GET['action'] == "album" && $lncln->user->isUser){
	$lncln->changeAlbum($_POST['id'], $_POST['album']);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra . "#" . $_POST['id']);
	exit();
}
?>
