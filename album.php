<?php
/**
 * album.php
 * 
 * Lists albums if no $_GET['album'], otherwise lists that album
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

$lncln = new lncln("album", array($_GET['album'], $_GET['img']));

require_once(ABSPATH . "includes/iconActions.php");

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
	
	echo "You're viewing " . $row['name'] . "\n <br />";
	
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
