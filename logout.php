<?
/**
 * logout.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0 $Id$
 * @license license.txt GNU General Public License
 * 
 * @package lncln
 */

require_once("config.php");
require_once("includes/functions.php");

connect();

if(isset($_COOKIE['username'])){
	setcookie("username", "", time() - (60 * 60 * 24));
	setcookie("password", "", time() - (60 * 60 * 24));
}


require_once("includes/header.php");

if(isset($_COOKIE['username'])){
?>
	You are now logged out.
<?
}
else{
?>
	Please log in before you can log out.
<?}

require_once("includes/footer.php");
?>