<?
/**
 * class.php
 * 
 * Main class for the settings admin module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 
 
class Settings extends lncln{
	
	/**
	 * Changes a setting only if it really exists
	 * 
	 * @since 0.11.0
	 * @package lncln
	 * 
	 * @param $name string Config name
	 * @param $value string Config value
	 */
	function changeSetting($name, $value){
		$sql = "SELECT name FROM settings WHERE name = '" . $name . "'";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) == 1){
			$sql = "UPDATE settings SET value = '" . $value ."' WHERE name = '" . $name . "'";
			mysql_query($sql);
		}
	}
	
	/**
	 * Lists themes installed
	 * 
	 * @since 0.11.0
	 * @package lncln
	 * 
	 * @return string Select of themes
	 */
	function listThemes(){
		$tempThemes = scandir(ABSPATH . "theme/");
		$themes = array();
		
		for($i = 2; $i <= (count($tempThemes) - 1); $i++){
			if($tempThemes[$i] == "index.html" || $tempThemes[$i] == ".svn"){
				continue;
			}
			$themes[] = $tempThemes[$i];
		}
		
		return $themes;
	}
	
	/**
	 * Lists groups in a select style.
	 * 
	 * @since 0.12.0
	 * @package lncln
	 * 
	 * @return string Contains a select for all groups
	 */
	function listGroups(){
		$sql = "SELECT id, name FROM groups WHERE 1";
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_assoc($result)){
			$groups[] = array(  "id" => $row['id'],
								"name" => $row['name']
								);
		}
		
		$select = "<select name='defaultGroup'>";
		
		foreach($groups as $group){
			if($this->display->settings['defaultGroup'] == $group['id'])
				$selected = " selected ";
			
			$select .= "<option value='" . $group['id'] . "' " . $selected . ">" . $group['name'] . "</option>";
		}
		
		$select .= "</select>";
		
		return $select;
	}
}