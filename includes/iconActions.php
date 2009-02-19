<?
/**
 * iconActions.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.6.1 $Id$
 * 
 * @package lncln
 */ 


if(isset($_GET['thumb'])){
	$extra = "&thumb=true";
}

if($_GET['post'] == true){
	$lncln->upload();
	header("location:". URL . "index.php");
	exit();
}

if(isset($_GET['delete']) && $lncln->isAdmin){
	$deletion = $lncln->delete($_GET['delete']);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra);
	exit();
}

if(isset($_GET['obscene']) && $lncln->isLoggedIn){
	$obscene = $lncln->obscene($_GET['obscene']);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra);
	exit();
}

if(isset($_GET['rateUp']) && $lncln->isLoggedIn){
	//This should probably be handled by the function itself
	$rating = 1;
	if($lncln->isAdmin){
		$rating = 5;
	}
	$lncln->rate($_GET['rateUp'], $rating);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra);
	exit();
}

if(isset($_GET['rateDown']) && $lncln->isLoggedIn){	
	//This should probably be handled by the function itself
	$rating = -1;
	if($lncln->isAdmin){
		$rating = -5;
	}
	$lncln->rate($_GET['rateDown'], $rating);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra);
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

if(isset($_GET['refresh']) && $lncln->isLoggedIn){
	$id = stripslashes($_GET['refresh']);
	$id = mysql_real_escape_string($id);
	
	$sql = "SELECT type FROM images WHERE id = " . $id;
	$result = mysql_query($sql);
	
	if(mysql_num_rows($result) == 1){
		$row = mysql_fetch_assoc($result);
		$lncln->thumbnail($id . "." . $row['type']);
	}
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra);
	exit();
}

if($_GET['caption'] && $lncln->isLoggedIn){
	$lncln->caption($_POST['id'], $_POST['caption']);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra);
	exit();
}

if($_GET['tag'] && $lncln->isLoggedIn){
	$lncln->tag($_POST['id'], $_POST['tags']);
	header("location:" . URL . $lncln->script . "?img=" . $_GET['img'] . $extra);
	exit();
}
?>
