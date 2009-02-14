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
 * @since 0.5.0
 */
$config['title'] = "The Archive";

$config['mysql']['server'] = "";
$config['mysql']['database'] = "";
$config['mysql']['user'] = "";
$config['mysql']['password'] = "";

/**
 * Config as of
 * @since 0.6.0
 */

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
 
define("CURRENT_IMG_DIRECTORY", getcwd() . "/img/");

?>