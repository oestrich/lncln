<?php
/**
 * admin.php
 * 
 * Admin panel
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Admin's Admin class
 * @since 0.13.0
 * 
 * @package lncln
 */
class AdminAdmin extends Admin{
	public $set = false;
	
	public function set_vars(){
		$this->actions = $this->lncln->modules['admin']->actions;
		$this->info = $this->lncln->modules['admin']->info;
		$this->modules = $this->lncln->modules['admin']->modules;
	}
	
	public function actions(){
		$actions = array(
			'urls' => array(
				'Main' => array(
					'info' => 'Currently installed modules',
					),
				),
			'quick' => array(
				'info'
				),
			);
		
		return $actions;
	}

	/**
	 * Prints out the info for modules
	 * @since 0.13.0
	 */
	public function info(){
		if(!$this->set){
			$this->set_vars();
		}
		
		foreach($this->info as $info){
			$requires = join(" ", $info['requires']);
			
			if($requires == ""){
				$requires = "No requirements";
			}
			
			echo "<span class='admin_link'>";
			if($this->check_module($info['name'])){
				echo "<a href='" . URL . "admin/" . $info['name']. "/'>" . $info['name'] . "</a>";
			}
			else{
				echo $info['name'];
			}
			echo "</span>\n";
			echo "<p class='admin_description'>" . $info['description'] . "</p>\n";
			echo "<table>\n";
			echo "<tr><td>Version:</td><td>" . $info['version'] . "</td></tr>\n";
			echo "<tr><td>Package:</td><td>" . $info['package'] . "</td></tr>\n";
			echo "<tr><td>Requires:</td><td>" . $requires . "</td></tr>\n";
			echo "</table>\n";
			echo "<br />";
		}
	}
}