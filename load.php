<?
/**
 * load.php
 * 
 * One of the most important files, gets the entire software going
 * Every page should include this
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

/**
 * Defines ABSPATH which is the absolute directory for lncln
 * Use to include other files
 */
define("ABSPATH", dirname(__FILE__) . "/");

if(file_exists(ABSPATH . "config.php")){
	include_once(ABSPATH . "config.php");	
}
else{
	echo "Please configure your config.php file.  Use config-sample.php as a base.<br />\n" .
			"Also don't forget to install the database, located at <a href=\"mysql.sql\">mysql.sql</a>";
	die();
}

include_once(ABSPATH . "includes/functions.php");
include_once(ABSPATH . "includes/class.lncln.php");
include_once(ABSPATH . "includes/class.display.php");
include_once(ABSPATH . "includes/class.user.php");
include_once(ABSPATH . "includes/class.db.php");

$db = new Database();


$lncln = new lncln();
