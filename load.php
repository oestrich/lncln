<?
/**
 * load.php
 * 
 * One of the most important files, gets the entire software going
 * Every page should include this
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

define("ABSPATH", dirname(__FILE__) . "/");

if(file_exists(ABSPATH . "config.php")){
	require_once(ABSPATH . "config.php");	
}
else{
	echo "Please configure your config.php file.  You can use config-sample.php as a base.";
	die();
}

require_once(ABSPATH . "includes/functions.php");

connect();

$sql = "SHOW TABLES LIKE \"images\"";
$result = mysql_query($sql);
if(mysql_num_rows($result) < 1){
	echo "Please install the database.  It's located in <a href=\"mysql.sql\">mysql.sql</a>";
	die();
}

$lncln = new lncln();

?>
