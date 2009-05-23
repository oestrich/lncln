<?
/**
 * actions.php
 * 
 * This file adds actions to a page that handles the icons underneath images
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */ 

if(isset($_GET['thumb'])){
	$extra = "&thumb=true";
}

$extra .= str_replace("&amp;", "&", $lncln->extra);

$scriptLocation = $lncln->script == "image.php" ? 
	$scriptLocation = URL . $lncln->script . "?img=" . $_GET['img'] . $extra :
	URL . $lncln->script . "?page=" . $_GET['page'] . $extra;

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

if(isset($_GET['refresh']) && $lncln->user->permissions['refresh'] == 1){
	$id = $this->db->prep_sql($_GET['refresh']);
	
	$sql = "SELECT type FROM images WHERE id = " . $id;
	$result = mysql_query($sql);
	
	if(mysql_num_rows($result) == 1){
		$row = mysql_fetch_assoc($result);
		$lncln->thumbnail($id . "." . $row['type']);
	}
	header("location:" . $scriptLocation . "#" . $_GET['refresh']);
	exit();
}

if($lncln->module == "thumbnail"){
	$_SESSION['thumbnail'] = $lncln->params[0] == "on" ? 1 : 0;
	header("location:" . URL . $_SESSION['URL']);
	exit();
}

foreach($lncln->modules as $module){
	if($lncln->action == true && $lncln->module == strtolower($module->name) && $lncln->user->permissions[strtolower($module->name)] == 1){
		if(!isset($_POST['id']) || $_POST['id'] == "")
			$id = end($lncln->params);
		else
			$id = $_POST['id'];
		if(method_exists($module, "edit")){
			//$_POST[$lncln->module] is the POST field that corresponds to the module in question
			//$lncln->params[0] is a sub_action
			$module->edit($id, array($_POST[$lncln->module], $lncln->params[0]));
		}

		header("location:" . URL . $_SESSION['URL'] . "#" . $id);
		exit();
	}
}
?>
