<?
/**
 * manage.php
 * 
 * Lists all of the users, can delete them
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

if($_GET['subAction'] == "delete"){
	$lncln->deleteUser($_GET['user']);
}

?>

	Users:<br />
	<ul>
<?
	foreach($lncln->getUsers() as $user){
		if($user['name'] == "Anonymous")
			continue;
		
		echo "\t\t\t<li>" . $user['name'] . " <a href='" . createLink("edit", array("user" => $user['id'])) . "'>Edit</a> " .
				"<a href='" . createLink("manage", array("subAction" => "delete", "user" => $user['id'])) . "'>Delete</a></li>\n";
	}	
?>
	</ul>