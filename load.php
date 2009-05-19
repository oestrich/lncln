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
 */ 

define("ABSPATH", dirname(__FILE__) . "/");

if(file_exists(ABSPATH . "config.php")){
	include_once(ABSPATH . "config.php");	
}
else{
	echo "Please configure your config.php file.  You can use config-sample.php as a base.";
	die();
}

include_once(ABSPATH . "includes/functions.php");
include_once(ABSPATH . "includes/db.php");

$db = new Database();

$sql = "SHOW TABLES LIKE 'images'";
$result = $db->query($sql);
if($db->num_rows() < 1){
	echo "Please install the database.  It's located in <a href=\"mysql.sql\">mysql.sql</a>";
	die();
}

$lncln = new lncln();

?>
