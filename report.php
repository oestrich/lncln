<?
/**
 * report.php
 * 
 * Reports an image and sticks the image into the queue if it has more than 5 reports
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

$lncln = new lncln();

if($lncln->user->isUser){
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