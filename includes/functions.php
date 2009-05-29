<?
/**
 * functions.php
 * 
 * Contains useful functions
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0  $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Creates a temporary name for uploads, returns a string
 * that is 25 random characters, a-zA-Z0-9
 * @since 0.10.0
 * 
 * @param string $name The name of the file that was uploaded, so it can pull the type
 * 
 * @return string 25 characters to use as a name for storing the temporary image
 */
function tempName($name){
	$typeTmp = split("\.", $name);
	$type = $typeTmp[count($typeTmp) - 1];
	
	$array = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
				   'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
				   'w', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7',
				   '8', '9', '0', 'A', 'B', 'c', 'D', 'E', 'F', 'G', 'H',
				   'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S',
				   'T', 'U', 'V', 'w', 'X', 'Y', 'Z'
				  );
	$string = "";
	
	for($i = 0; $i < 25; $i++){
	        $string .= $array[rand(0, count($array))];
	}
	
	return $string . '.' . $type;
}

/**
 * Creates an input based on type and other values
 * @since 0.13.0
 * 
 * @param $input array Keys: name, type, value
 * @param $id mixed Image that is being edited, id or temporary name
 * 
 * @return string Input string for form
 */
function createInput($input, $id, $extra = ""){
	switch($input['type']){
		case "text":
			return "<input type='text' name='images[$id][" . $input['name'] . "]' value='" . $input['value'] . "' " . $extra . " />";
		case "textarea":
			return "<textarea name='images[$id][" . $input['name'] . "]' " . $extra . " rows='10' cols='50'>" . $input['value'] . "</textarea>";
		case "select":
			$output = "<select name='images[$id][" . $input['name'] . "]' " . $extra . ">";
			
			foreach($input['options'] as $option){
				$selected = $option['name'] == $input['value'] ? "selected" : "";
				
				$output .= "<option value='" . $option['id'] . "' $selected>" . $option['name'] . "</option>";
			}
			
			$output .= "</select>";
			
			return $output;
	}
}

/**
 * Creates a form based on the array given
 * @since 0.13.0
 */
function create_form($form){
	$output = "";
	
	if($form['file'] == true){
		$file = " enctype='multipart/form-data' ";
	}
	
	$output .= "<form action='" . URL . $form['action'] . "' method='" . $form['method'] ."' $file>\n";
	$output .= "\t<div>\n";
	$output .= "\t\t<table>\n";
	
	foreach($form['inputs'] as $input){
		$output .= "\t\t\t<tr>\n";
		
		switch($input['type']){
			case 'text':
				$output .= "\t\t\t\t<td>" . $input['title'] . "</td>\n";
				$output .= "\t\t\t\t<td><input type='text' name='" . $input['name'] . "' value='" . $input['value'] . "'/></td>\n";
				break;
			case 'hidden':
				$output .= "\t\t\t\t<td colspan='2'><input type='hidden' name='" . $input['name'] ."' value='" . $input['value'] . "'></td>\n";
				break;
			case 'select':
				$output .= "\t\t\t\t<td>" . $input['title'] . "</td>\n";
				$output .= "\t\t\t\t<td><select name='" . $input['name'] . "'>\n";
				foreach($input['options'] as $option){
					if(isset($option['selected']) && $option['selected'] == true){
						$selected = " selected";
					}
					else{
						$selected = "";
					}
					
					$output .= "\t\t\t\t\t<option value='" . $option['value'] . "'$selected>" . $option['name'] . "</option>\n";
				}
				$output .= "\t\t\t\t</select></td>\n";
				break;
			case 'textarea':
				$output .= "\t\t\t\t<td>" . $input['title'] . "</td>\n";
				$output .= "\t\t\t\t<td><textarea name='" . $input['name'] . "'  rows='10' cols='50'>" . $input['value'] . "</textarea>";
				break;
			case 'description':
				$output .= "\t\t\t\t<td colspan='2'>" . $input['title'] . "</td>\n";
				break;
		}
		
		$output .= "\t\t\t</tr>\n";
	}
	
	$output .= "\t\t</table>\n";
	$output .= "\t\t<input type='submit' value='" . $form['submit'] . "'/>\n";
	$output .= "\t</div>\n";
	$output .= "</form>\n";
	
	echo $output;
	return $output;
}

/**
 * Easy access to the $db object
 * @since 0.13.0
 * 
 * @global Database Instance of Database
 * 
 * @return Database Reference to the Database instance
 */
function get_db(){
	global $db;
	
	return $db;
}

/**
 * Easy access to $lncln
 * @since 0.13.0
 * 
 * @global lncln Main lncln instance
 * 
 * @return lncln Main lncln instance
 */
function get_lncln(){
	global $lncln;
	
	return $lncln;
}
