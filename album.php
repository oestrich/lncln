<?php
/**
 * album.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.8.0 $Id$
 * @license license.txt GNU General Public License
 * 
 * @package lncln
 */

require_once("load.php");

$lncln = new lncln("album", array($_GET['album'], $_GET['img']));
$lncln->loggedIn();

$lncln->img();

require_once("includes/header.php");

if(!isset($_GET['album']) || $_GET['album'] == ""){
	foreach($lncln->getAlbums() as $album){
?>
			<a href="<?=$lncln->script;?>?album=<?=$album['id'];?>"><?=$album['name'];?></a><br />
<?
	}
}
else{
	$sql = "SELECT name FROM albums WHERE id = " . $lncln->album;
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	
	echo $row['name'] . ": \n <br />";
	
	echo $lncln->prevNext();
	
	require_once("includes/listImages.php");
	
?>
		<div id='bPrevNext'>
<?
	echo $lncln->prevNext();
?>
		</div>
<?
}



require_once("includes/footer.php");
?>
