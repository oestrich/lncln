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

foreach($lncln->listThemes() as $theme){
	$selected = $theme == $lncln->display->settings['theme'] ? " selected " : "";
	
	$themes .= "<option value='" . $theme . "' " . $selected . ">" . $theme . "</option>"; 
}
$themes .= "</select>";
?>

Change the board settings: <br />

<form action="<?=createLink("manage", array("subAction" => "edit"));?>" method="post" />
	<div>
		<table>
			<tr>
				<td>Title:</td>
				<td><input type="text" name="title" value="<?=$lncln->display->settings['title'];?>"/></td>
			</tr>
			<tr>
				<td>Images/Page:</td>
				<td><input type="text" name="perpage" value="<?=$lncln->display->settings['perpage'];?>" /></td>
			</tr>
			<tr>
				<td>Theme:</td>
				<td><?=$themes;?></td>
			</tr>
		</table>
		<input type="submit" value="Submit" />
	</div>
</form>