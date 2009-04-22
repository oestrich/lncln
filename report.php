<?
/**
 * report.php
 * 
 * Reports an image and sticks the image into the queue if it has more than 5 reports
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

if($lncln->user->permissions['rate'] == 0){
	include(ABSPATh . "includes/header.php");
	echo "You can't report images";
	include(ABSPATH . "includes/footer.php");
	exit();
}

$image = prepareSQL($_GET['img']);

$sql = "UPDATE images SET report = report + " . $lncln->user->permissions['rateValue'] . " WHERE id = " . $image . " LIMIT 1";
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