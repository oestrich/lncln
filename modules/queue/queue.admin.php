<?php
/**
 * queue.admin.php
 * 
 * Admin class for the Queue module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Admin class for Queue module
 * @since 0.13.0
 * 
 * @package lncln
 */
class QueueAdmin extends Queue{
	
	/**
	 * Returns actions required by Queue module
	 * @since 0.13.0
	 * 
	 * @return array Actions array
	 */
	public function actions(){
		$action = array(
			'urls' => array(
				'Main' => array(
					'manage' => 'Manage albums',
					),
				),
			'quick' => array(
				'manage',
				),
			);
		
		return $action;
	}
	
	/**
	 * Manage queue page
	 * @since 0.13.0
	 */
	public function manage(){
		if($this->lncln->params[2] == "delete" && is_array($_POST['approve'])){
			foreach($_POST['approve'] as $key => $value){
				$this->lncln->delete($key);
			}
			$this->lncln->display->message("Queue updated.");
		}
		
		if($_POST['approve'] != "" || $_POST['check'] != ""){
			if(!is_array($_POST['check'])){
				$_POST['check'] = array();
			}
			if(!is_array($_POST['approve'])){
				$_POST['approve'] = array();
			}
			
			foreach($_POST['check'] as $key => $value){
				foreach($this->lncln->modules as $modKey => $module){
					if(method_exists($module, "edit")){
						$module->edit($key, array($_POST['images'][$key][$modKey]));
					}
				}
			}
			
			foreach($_POST['approve'] as $key => $value){
				$this->lncln->dequeue($key);
			}	
			
			$this->lncln->display->message("Queue updated");
		}
		
		$this->lncln->modules['queue']->in_queue = true;
		
		$this->prepare_queue();
		
		$this->lncln->get_data();
		
		echo "There are " . $this->check_queue() . " items in the queue.\n";
		
		echo $this->lncln->prevNext();
		
		echo "\n<form id='queue' action='" . URL . "admin/Queue/manage/' method='post'>\n";
		
		foreach($this->lncln->images as $image){
			echo "\t<div id='" . $image['id'] . "' class='modDiv'>\n";
			echo "\t\t<input type='hidden' name='check[" . $image['id'] . "]' id='check" . $image['id'] . "' value='0' />\n";
			echo "\t\tApprove: <input type='checkbox' name='approve[" . $image['id'] . "]' id='approve" . $image['id'] . "' style='height: 35px; width: 35px;' /><br />\n";
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
		echo "\t<input type='submit' value='Delete Selected' onclick='document.getElementById(\"queue\").action = \"" . URL . "admin/Queue/manage/delete\";' />\n";
		echo "</form>\n\n";
		
		echo "<form action='" . URL . "admin/Queue/manage/' method='post'>\n";
		echo "\t<div>\n";
	    foreach($this->lncln->images as $image){
			echo "\t\t<input type='hidden' name='approve[" . $image['id'] . "]' value='1' />\n";
	    }
		echo "\t\t<input type='submit' value='Approve All' />\n";
		echo "\t</div>\n";
		echo "</form>\n";
		
		echo $this->lncln->prevNext();
	}
	
	
	protected function prepare_queue(){
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
		if($row['COUNT(*)'] > 0){
			$this->db->query("SELECT COUNT(id) FROM images WHERE queue = 1");
			$row = $this->db->fetch_one();

			$this->lncln->maxPage = $row['COUNT(id)'];
			$this->lncln->maxPage = ceil($this->lncln->maxPage / $this->lncln->display->settings['perpage']);
			
			if(!isset($_GET['page'])){
				$this->lncln->page = 1;
			}
			else{
				if(is_numeric($_GET['page'])){
					$this->lncln->page = $_GET['page'];	
				}
				else{
					$this->lncln->page = 1;
				}
			}
			
			$offset = ($this->lncln->page - 1) * $this->lncln->display->settings['perpage'];
			
			$sql = "SELECT id FROM `images` WHERE queue = 1 ORDER BY id DESC LIMIT " . $offset . ", " . $this->lncln->display->settings['perpage'];
			$this->db->query($sql);
			
			foreach($this->db->fetch_all() as $row){				
				$this->lncln->imagesToGet[] = $row['id'];
			}
		}
	}
}