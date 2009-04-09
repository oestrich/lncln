<?
/**
 * manage.php
 * 
 * Let's an admin manage albums
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
 
?>
	Albums: <br />
	<ul>
<?
	foreach($lncln->getAlbums() as $album){
		echo "\t\t\t<li>" . $album['name'] . " <a href='albums.php?action=delete&amp;album=" . $album['id'] . "'>Delete</a></li>\n";
	}	
?>
	</ul>