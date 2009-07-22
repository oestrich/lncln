<?php
/**
 * image.admin.php
 * 
 * Image module admin file
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Image admin class
 * @since 0.13.0
 * 
 * @package lncln
 */
class ImageAdmin extends Image{
	/**
	 * Returns actions required by Image module
	 * @since 0.13.0
	 * 
	 * @return array Actions array
	 */
	public function actions(){
		$action = array(
			'urls' => array(
				'Main' => array(
					'moderate' => 'Moderate images',
					),
				),
			'quick' => array(
				'moderate',
				),
			);
		
		return $action;
	}
	
	/**
	 * Moderate images
	 * @since 0.13.0
	 */
	public function moderate(){
		if($this->lncln->params[2] == "delete" && is_array($_POST['approve'])){
			foreach($_POST['approve'] as $key => $value){
				$this->delete($key);
			}
			$this->lncln->display->message("Images deleted. Click " .
				"<a href='" . URL . "admin/Image/moderate/" . $this->lncln->params[3] . 
				"'>here</a> to go back to moderation.");
		}
		
		if($_POST['approve'] != "" || $_POST['check'] != ""){
			if(!is_array($_POST['check'])){
				$_POST['check'] = array();
			}
			
			foreach($_POST['check'] as $key => $value){
				foreach($this->lncln->modules as $modKey => $module){
					if(method_exists($module, "edit")){
						$module->edit($key, array($_POST['images'][$key][$modKey]));
					}
				}
			}
			
			$this->lncln->display->message("Images updated. Click " .
				"<a href='" . URL . "admin/Image/moderate/" . $this->lncln->params[2] . 
				"'>here</a> to go back to moderation.");
		}
		
		$this->lncln->page = (int)$this->lncln->params[2];
		
		if($this->lncln->page == "")
			$this->lncln->page = 1;
		
		$this->lncln->modules['index']->prepare_index();
		
		$this->lncln->get_data();
		
		echo $this->lncln->prevNext();
		
		echo "\n<form id='queue' action='" . URL . "admin/Image/moderate/" . $this->lncln->page . "' method='post'>\n";
		
		foreach($this->lncln->images as $image){
			echo "\t<div id='" . $image['id'] . "' class='modDiv'>\n";
			echo "\t\t<input type='hidden' name='check[" . $image['id'] . "]' id='check" . $image['id'] . "' value='0' />\n";
			echo "\t\t<input type='checkbox' name='approve[" . $image['id'] . "]' id='approve" . $image['id'] . "' style='height: 35px; width: 35px;' /><br />\n";
			echo "\t\t<a href='" . URL . "images/full/" . $image['file'] . "' target='_blank' class='modImage''><img src='" . URL . "images/thumb/" . $image['file'] . "' /></a>\n";
			echo "\t\t<div class='modForms'>\n";
			echo "\t\t\t<input type='hidden' name='images[" . $image['id'] . "][id]' value='" . $image['id'] . "' /><br />\n";
			echo "\t\t\t<table>\n";
			foreach($this->lncln->modules as $module){
				if(!method_exists($module, "moderate"))
					continue;
				
				echo "\t\t\t<tr>\n";
				echo "\t\t\t\t<td>" . $module->displayName . ":</td>\n";
				echo "\t\t\t\t<td>" . createInput($module->moderate($image['id']), $image['id'], " onfocus=\"queueCheck('" . $image['id'] . "')\" ") . "</td>\n";
				echo "\t\t\t</tr>\n";

			}
			
			echo "\t\t\t</table>\n";
			echo "\t\t</div>\n";
			echo "\t</div>\n\n";
		}
			
		echo "\t<input type='submit' value='Submit' />\n";
		echo "\t<input type='submit' value='Delete Selected' onclick='document.getElementById(\"queue\").action = \"" . 
				URL . "admin/Image/moderate/delete/" . $this->lncln->page . "\";' />\n";
		echo "</form>\n\n";
	}
}
