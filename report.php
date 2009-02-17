<?
/**
 * report.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.6.0 $Id$
 * 
 * @package lncln
 */

require_once("config.php");
require_once("includes/functions.php");

connect();

$lncln = new lncln();
$lncln->loggedIn();


if($lncln->isLoggedIn){
	$amount = 5;
}
else{
	$amount = 1;
}

$image = $_GET['img'];
$image = stripslashes($image);
$image = mysql_real_escape_string($image);

$sql = "UPDATE images SET report = report + " . $amount . " WHERE id = " . $image . " LIMIT 1";
mysql_query($sql);

if(mysql_affected_rows() == 1){
	$sql = "SELECT report FROM images WHERE id = " . $image;
	$result = mysql_query($sql);
	
	$result = mysql_fetch_assoc($result);
	
	if($result['report'] >= 5){
		$sql = "UPDATE images SET queue = 1 WHERE id = " . $image . " LIMIT 1";
		mysql_query($sql);
	}
}

require_once("includes/header.php");
?>
	<br />
	The image <?echo $image;?> has been reported.  Thank you.
<?
require_once("includes/footer.php");
?>