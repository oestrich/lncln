<?
/**
 * logout.php
 * 
 * What do you think it does?
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");


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