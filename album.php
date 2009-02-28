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


require_once("includes/header.php");

if(!isset($_GET['album']) || $_GET['album'] == ""){
	foreach($lncln->getAlbums() as $album){
?>
			<a href="<?=$lncln->script;?>?album=<?=$album['id'];?>"><?=$album['name'];?></a><br />
<?
	}
}
else{
	$lncln->img();
	require_once("includes/listImages.php");
}



require_once("includes/footer.php");
?>
