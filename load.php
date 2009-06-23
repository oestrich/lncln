<?php
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
 * @var string Defines ABSPATH which is the absolute directory for lncln
 */
define("ABSPATH", dirname(__FILE__) . "/");

if(file_exists(ABSPATH . "config.php")){
	/** Configuration settings */
	include_once(ABSPATH . "config.php");	
}
else{
	echo "Please configure your config.php file.  Use config-sample.php as a base.<br />\n" .
			"Also don't forget to install the database, located at <a href=\"mysql.sql\">mysql.sql</a>";
	die();
}

/** Main functions */
include_once(ABSPATH . "includes/functions.php");
/** lncln class */
include_once(ABSPATH . "includes/class.lncln.php");
/** Display class */
include_once(ABSPATH . "includes/class.display.php");
/** User class */
include_once(ABSPATH . "includes/class.user.php");
/** Database class */
include_once(ABSPATH . "includes/class.db.php");
/** Module Class */
include_once(ABSPATH . "includes/class.module.php");

/**
 * @global Database $GLOBALS['db']
 * @name $db
 * @var Database Instance of the Database class
 */
$GLOBALS['db'] = new Database();

/**
 * @global lncln $GLOBALS['lncln']
 * @name $lncln
 * @var lncln Main instance of lncln
 */
$GLOBALS['lncln'] = new lncln();

/**
 * This little thing caused a lot of problems before it was moved out.
 */
$lncln->loadModules();
