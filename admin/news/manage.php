<?
/**
 * manage.php
 * 
 * Displays news to be changed
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if($_GET['subAction'] == "delete"){
	$lncln->deleteNews($_GET['news']);
}

?>

	News:<br />
	<ul>
<?
	foreach($lncln->getNews() as $news){
		echo "\t\t\t<li>" . $news['title'] . " <a href='" . createLink("edit", array("news" => $news['id'])) . "'>Edit</a> " .
				"<a href='" . createLink("manage", array("subAction" => "delete", "news" => $news['id'])) . "'>Delete</a></li>\n";
	}	
?>
	</ul>
 