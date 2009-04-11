<?
/**
 * index.php
 * 
 * Main page for the settings admin module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

require_once("../../load.php");

include("class.php");

$lncln = new Skeleton();

include("../admin.php");

require_once(ABSPATH . "includes/header.php");

if(file_exists($_GET['action'] . ".php")){
	include($_GET['action'] . ".php");
}
else{
	echo "That action does not exist";
}

require_once(ABSPATH . "includes/footer.php");
?>