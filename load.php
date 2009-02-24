<?
/**
 * load.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.7.0 $Id$
 * 
 * @package lncln
 */ 


if(file_exists('config.php')){
	require_once("config.php");	
}
else{
	echo "Please configure your config.php file.  You can use config-sample.php as a base.";
	die();
}

require_once(ABSPATH . "includes/functions.php");

connect();
?>
