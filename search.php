<?
/**
 * search.php
 * 
 * Boots a user back to the homepage if $_GET['search'] is empty
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

$lncln->search(array($_GET['search'], $_GET['img']));

if(!isset($_GET['search']) || $_GET['search'] == ""){
	header("location:index.php");
	exit();
}

include_once(ABSPATH . "includes/iconActions.php");

$lncln->img();

include_once(ABSPATH . "includes/header.php");

?>
	You searched for: <?echo $_GET['search'];?> <br />
<?

echo $lncln->prevNext();

include_once(ABSPATH . "includes/listImages.php");

?>
	<div id='bPrevNext'>
<?
echo $lncln->prevNext();
?>
	</div>
<?

include_once(ABSPATH . "includes/footer.php");

?>