<?
/**
 * search.php
 * 
 * @author Eric Oestrich
 * @version 0.6.0 $Id$
 * 
 * @package lncln
 */

require_once("config.php");
require_once("includes/functions.php");

connect();

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

$sql = "SELECT MAX(id) FROM images LIMIT 1";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);

$start = $row['MAX(id)'];

require_once("includes/listImages.php");

require_once("includes/footer.php");

?>