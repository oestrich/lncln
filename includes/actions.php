<?
/**
 * actions.php
 * 
 * This file adds actions to a page that handles the icons underneath images
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

/*
 * Doesn't work
 * @todo make it its own module or integrate it into the module with thumbnail
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
*/

if($lncln->module == "thumbnail"){
	$_SESSION['thumbnail'] = $lncln->params[0] == "on" ? 1 : 0;
	header("location:" . URL . $_SESSION['URL']);
	exit();
}

foreach($lncln->modules as $module){
	if($lncln->action == true && $lncln->module == strtolower($module->name) && 
		$lncln->user->permissions[strtolower($module->name)] == 1){
		
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
