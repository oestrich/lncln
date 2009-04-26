<?
/**
 * logout.php
 * 
 * What do you think it does?
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

if(isset($_COOKIE['username'])){
	setcookie("username", "", time() - (60 * 60 * 24));
	setcookie("password", "", time() - (60 * 60 * 24));
}

if(isset($_COOKIE['username'])){
	$lncln->display->message("You are now logged out.");
}
else{
	$lncln->display->message("Please log in before you can log out.");
}
?>