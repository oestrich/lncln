<?
/**
 * manage.php
 * 
 * Displays news to be changed
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
?>

	News:<br />
	<ul>
<?
	foreach($lncln->getNews() as $news){
		echo "\t\t\t<li>" . $news['title'] . " <a href='" . createLink("edit", array("user" => $news['id'])) . "'>Edit</a> " .
				"<a href='" . createLink("manage", array("subAction" => "delete", "user" => $news['id'])) . "'>Delete</a></li>\n";
	}	
?>
	</ul>
 