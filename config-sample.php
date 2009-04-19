<?
/**
 * config.php
 * 
 * Sample config.php
 * Edit to suit your needs
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
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

define("URL", "/");
define("SERVER", "");

?>