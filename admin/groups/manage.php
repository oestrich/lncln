<?
/**
 * manage.php
 * 
 * Let's an admin manage groups?
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
?>

	Groups:<br />
	<ul>
<?
	foreach($lncln->getGroups() as $group){
		if($user['name'] == "Anonymous")
			continue;
		
		echo "\t\t\t<li>" . $group['name'] . " <a href='" . createLink("edit", array("group" => $group['id'])) . "'>Edit</a> " .
				"<a href='" . createLink("manage", array("subAction" => "delete", "group" => $group['id'])) . "'>Delete</a></li>\n";
	}	
?>
	</ul>