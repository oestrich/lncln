<?
/**
 * manage.php
 * 
 * Let's an admin manage groups?
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if($_GET['subAction'] == "delete"){
	if(is_numeric($_POST['group'])){
		$lncln->display->message($lncln->deleteGroup($_GET['group'], $_POST['group']));
	}
	
	$group = $lncln->getGroup($_GET['group']);
	
	echo "Move all users from " . $group['name'] . " to which group?<br />";
	echo "<form action='" . createLink("manage", array("subAction" => "delete", "group" => $group['id']), true) . "' method='post'>";
	echo "<select name='group'>";
	foreach($lncln->getGroups() as $group){
		if($group['id'] == $_GET['group'])
			continue;
		echo "<option value='" . $group['id'] . "'>" . $group['name'] ."</option>";
	}
	echo "</select>";
	echo "<input type='submit' value='Submit' />";
	echo "</form>";
	include_once(ABSPATH . "includes/footer.php");
	exit();
}
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