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

$lncln = new lncln("search", array($_GET['search'], $_GET['img']));
$lncln->loggedIn();


if(!isset($_GET['search']) || $_GET['search'] == ""){
	header("location:index.php");
	exit();
}

$lncln->img();

require_once("includes/header.php");

?>
	You searched for: <?echo $_GET['search'];?> <br />
<?

echo $lncln->prevNext();

require_once("includes/listImages.php");

?>
	<div id='bPrevNext'>
<?
echo $lncln->prevNext();
?>
	</div>
<?

require_once("includes/footer.php");

?>