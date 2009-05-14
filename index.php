<?
/**
 * index.php
 * 
 * Main page, does the "index" action
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */ 

require_once("load.php");

include_once(ABSPATH . "includes/iconActions.php");

if(isset($lncln->module) && $lncln->module != ""){
	if(method_exists($lncln->modules[$lncln->module], "index")){
		$lncln->modules[$lncln->module]->index();
	}
	else{
		$lncln->display->message("That module does not exist");
	}
	exit();
}

if(file_exists($lncln->module . ".php")){
	include_once($lncln->module . ".php");
	exit();
}
?>