<?
/**
 * search.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.8.0 $Id$
 * @license license.txt GNU General Public License
 * 
 * @package lncln
 */

require_once("load.php");

$lncln = new lncln("search", array($_POST['search']));
$lncln->loggedIn();


if(!isset($_POST['search']) || $_POST['search'] == ""){
	header("location:index.php");
	exit();
}

$lncln->img();

require_once("includes/header.php");

?>
	You searched for: <?echo $_POST['search'];?> <br />
<?

echo $lncln->prevNext();

require_once("includes/listImages.php");

require_once("includes/footer.php");

?>