<?
/**
 * config.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.6.1 $Id$
 * 
 * @package lncln
 */

session_start();

/**
 * Config as of
 * @since 0.6.0
 */

define("VERSION", "0.6.1");
define("TITLE", "The Archive");

/**
 * Database Configuration
 * 
 * @since 0.6.0
 */
define("DB_SERVER", "localhost");
define("DB_DATABASE", "lncln");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");

/**
 * General Configurations
 * 
 * @since 0.6.0
 */
 
define("ABSPATH", dirname(__FILE__) . "/");
define("CURRENT_IMG_DIRECTORY", ABSPATH . "images/full/");

$script = split("/", $_SERVER['SCRIPT_NAME']);
$script = $script[count($script) - 1];
$URL = str_replace($script, "", $_SERVER['SCRIPT_URL']);
if(strstr($URL, "admin")){
	define("URL", str_replace("admin/", "", $URL));
}
else{
	define("URL", $URL);
}

/**
 * Theme configurations
 * 
 * @since 0.6.1
 */
 
define("THEME", "bbl");
?>