<?
/**
 * index.php
 * 
 * Main page for the album admin module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

require_once("../../load.php");

include_once("../admin.php");

include_once("class.php");

$lncln = new AlbumsAdmin();

include_once(ABSPATH . "includes/header.php");

if(file_exists($_GET['action'] . ".php")){
	include_once($_GET['action'] . ".php");
}
else{
	echo "That action does not exist";
}

include_once(ABSPATH . "includes/footer.php");
?>