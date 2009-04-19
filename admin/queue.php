<?
/**
 * queue.php
 * 
 * Exactly what it sounds like
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("../load.php");

$lncln->queue();

include("admin.php");

if($_GET['action'] == "update"){
	$lncln->dequeue($_POST);
	header("location:" . URL . "admin/" . $lncln->script);
	exit();
}

include(ABSPATH . "includes/iconActions.php");

$lncln->img();

require_once("../includes/header.php");

$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
$result = mysql_query($sql);
$result = mysql_fetch_assoc($result);

if(isset($output)){
	echo $output . "<br />";
}

echo "There are " . $result['COUNT(*)'] . " items in the queue.";
?>
<br />
This page will show the first 50 items in the queue<br />
<form enctype="multipart/form-data" action="queue.php?action=update" method="post">
	<div class="queue">
<?
	include(ABSPATH . "includes/listImages.php");
?>
		<input type='submit' value='Submit' />
	</div>
</form>

require_once("../includes/footer.php");
?>