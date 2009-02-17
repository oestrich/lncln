<?
/**
 * config.php
 * 
 * @author Eric Oestrich
 * @version 0.6.0
 * 
 * @package lncln
 */

session_start();

/**
 * Config as of
 * @since 0.6.0
 */

define("VERSION", "0.6.0");
define("TITLE", "The Arcive");

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
 
define("ABSPATH", dirname(__FILE__));
 
define("CURRENT_IMG_DIRECTORY", ABSPATH . "/img/");

?>