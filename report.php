<?
/**
 * report.php
 * 
 * Reports an image and sticks the image into the queue if it has more than 5 reports
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

require_once("load.php");

if($lncln->user->permissions['report'] == 0){
	$lncln->display->message("You can't report images");
}

$image = $this->db->prep_sql($_GET['img']);

$sql = "UPDATE images SET report = report + " . $lncln->user->permissions['reportValue'] . " WHERE id = " . $image . " LIMIT 1";
mysql_query($sql);

if(mysql_affected_rows() == 1){
	$sql = "SELECT report FROM images WHERE id = " . $image;
	$result = mysql_query($sql);
	
	$row = mysql_fetch_assoc($result);
	
	if($row['report'] >= 5){
		$sql = "UPDATE images SET queue = 1 WHERE id = " . $image . " LIMIT 1";
		mysql_query($sql);
	}
}

$lncln->display->message("Image #" . $image . " has been reported.  Thank you.");
?>