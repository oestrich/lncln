<?
/**
 * config.php
 * 
 * Sample config.php
 * Edit to suit your needs
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

session_start();

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
define("CURRENT_IMG_DIRECTORY", ABSPATH . "images/full/");
define("CURRENT_IMG_TEMP_DIRECTORY", ABSPATH . "images/temp/");

//Include trailing slash in URL
define("URL", "/");
//Include your domain name, no slashes
define("SERVER", "");

//Key is folder, value is class name
$modules_enabled = array(
	"admin" => "Admin",
	"captions" => "Captions", 
	"tags" => "Tags", 
	"albums" => "Albums", 
	"report" => "Report",
	"ratings" => "Ratings", 
	"index" => "Index", 
	"image" => "Image",
	"obscene" => "Obscene",
	"queue" => "Queue",
	);
