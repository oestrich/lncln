<?php
/**
 * index.php
 * 
 * Main page, does the "index" action
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

/** Starts off lncln */
require_once("load.php");

/** Loads the actions for calls to edit() */
include_once(ABSPATH . "includes/actions.php");

ob_start();

if (isset($lncln->module) && $lncln->module != ""){
	if (method_exists($lncln->modules[$lncln->module], "index")){
		$lncln->modules[$lncln->module]->index();
	}
	else{
		if (file_exists($lncln->module . ".php")){
			/** If no module exists, try looking for a regular php file */
			include_once($lncln->module . ".php");
		}
		else{
			$lncln->display->message("That module does not exist");
		}
	}
}

$contents = ob_get_contents();
ob_end_clean();

$lncln->display->show_header();
echo $contents;
$lncln->display->show_footer();