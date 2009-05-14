<?
/**
 * manage.php
 * 
 * Let's an admin manage albums
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */
 
if($_GET['subAction'] == "delete"){
	$lncln->deleteAlbum($_GET['album']);
}

?>
	Albums: <br />
	<ul>
<?
	foreach($lncln->getAlbums(false) as $album){
		echo "\t\t\t<li>" . $album['name'] . " <a href='" . createLink("edit", array("album" => $album['id'])) . "'>Edit</a> " .
				"<a href='" . createLink("manage", array("subAction" => "delete", "album" => $album['id'])) . "'>Delete</a></li>\n";
	}	
?>
	</ul>
