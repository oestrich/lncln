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

$lncln = new lncln();
$lncln->loggedIn();


if(!isset($_POST['search']) || $_POST['search'] == ""){
	header("location:index.php");
	exit();
}

$lncln->search = $_POST['search'];
$lncln->img();

require_once("includes/header.php");

?>
	You searched for: <?echo $_POST['search'];?> <br />
<?

require_once("includes/listImages.php");

require_once("includes/footer.php");

?>