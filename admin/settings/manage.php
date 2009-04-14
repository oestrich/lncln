<?
/**
 * manage.php
 * 
 * Displays the settings to be changed
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
 
if($_GET['subAction'] == "edit"){
	foreach($_POST as $name => $value){
		$lncln->changeSetting($name, $value);
	}
	
	echo "Settings have been saved.  Click <a href='" . URL . "admin/'>here</a> to continue";
	include(ABSPATH . "includes/footer.php");
	exit();
} 

$themes = "<select name='theme'>";
$tempThemes = scandir(ABSPATH . "theme/");

for($i = 2; $i <= (count($tempThemes) - 1); $i++){
	if($tempThemes[$i] == "index.html"){
		continue;
	}
	$themes .= "<option value='" . $tempThemes[$i] . "'>" . $tempThemes[$i] . "</option>"; 
}
$themes .= "</select>";
?>

Change the board settings: <br />

<form action="<?=createLink("manage", array("subAction" => "edit"));?>" method="post" />
	<div>
		Title: <input type="text" name="title" /><br />
		Theme: <?=$themes;?><br />
		<input type="submit" value="Submit" />
	</div>
</form>