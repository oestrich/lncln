<?php
/**
 * class.php
 * 
 * Main admin module class
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Main class for the Admin module
 * @since 0.13.0
 * 
 * @package lncln
 */
class Admin extends Module{
	/**
	 * @var string Name of module
	 */
	public $name = "Admin";
	
	/**
	 * @var string Name that is displayed to users
	 */
	public $displayName = "Admin";
	
	/**
	 * @var string Contains information relating to modules
	 */
	public $info = array();
	
	/**
	 * @var array Instances of admin modules
	 */
	protected $modules = array();
	
	/**
	 * @var array Actions that modules have
	 */
	protected $actions = array();
	
	/**
	 * Called if the Module has it's own page
	 * Such as albums or search
	 * @since 0.13.0
	 */
	public function index(){
		$this->check_admin();
		$this->load_info();
		$this->load_admin_modules();

		$module = $this->lncln->params[0];
		$action = $this->lncln->params[1];
		
		ob_start();
		
		//If going to a module page, enter
		if($this->lncln->params[0] != ""){			
			//If just the top module page, show actions
			if($this->lncln->params[1] == ""){
				if($this->check_module($module)){
					echo "<span class='admin_link'><a href='" . URL . "admin/'>Admin</a></span><span class='admin_action'> - </span>"
							. "<span class='admin_link'>". $module . "</span><br /><br />\n";
					
					foreach($this->actions[$module]['urls'] as $name => $group){
						echo "<span style='font-size: 1.5em;'>" . $name . "</span><br />";
						foreach($group as $url => $name){
							if($name != ""){
								echo "<a href='" . URL . "admin/" . $module . "/$url/'>" . $name . "</a><br />\n";
							}
						}
					}
					
					$this->lncln->display->set_title("Admin - " . $module);
				}
				else{
					echo "Not a module";
				}
			}
			else{
				//Only do the correct action!
				if($this->check_action($module, $action)){
					echo "<span class='admin_link'><a href='" . URL . "admin/'>Admin</a></span><span class='admin_action'> - </span>" .
							"<span class='admin_link'><a href='" . URL . "admin/" . $module . "'>". $module .
							"</a></span><span class='admin_action'> - " . ucwords($action) . "</span><br />\n<br />\n";

					//This is because modules are loaded under the class name
					$this->modules[$this->get_module_class($module)]->$action();
					
					$name = ucwords($action);
					
					$this->lncln->display->set_title($name . " - " . $module);
				}
				else{
					echo "Not an action";
				}
			}
		}
		else{
			$this->show_index();
		}
		
		//This allows for the title to change while in the admin panel
		ob_end_flush();
	}
	
	/**
	 * Creates the link in the header
	 * @since 0.13.0
	 *
	 * @return string Link or form
	 */
	public function header_link(){
		if($this->lncln->user->permissions['isAdmin']){
			return "Admin: <a href='" . URL . "admin/'>Admin Panel</a> ";
		}
		
		return "";
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Loads currently enabled modules info
	 * @todo make this load all modules in the modules/ folder
	 * @since 0.13.0
	 */
	protected function load_info(){
		foreach($this->lncln->modules as $module){
			$modules[$module->name] = $module->name;
		}
		
		ksort($modules);
		
		foreach($modules as $module){
			$info = array();
			
			$name = strtolower($module) . "_info";
			
			if(function_exists($name)){
				$info = $name();
				$this->info[$info['name']] = $info;
			}
			else{
				continue;
			}
		}
	}
	
	/**
	 * Check admin status
	 * @since 0.13.0
	 */
	protected function check_admin(){
		if($this->lncln->user->permissions['isAdmin'] != 1){
			$this->lncln->display->message("You must be an admin to be here");
		}
	}
	
	/**
	 * Load the admin modules
	 * @since 0.13.0
	 */
	protected function load_admin_modules(){
		foreach($this->lncln->modules_enabled as $folder => $module){
			if(file_exists(ABSPATH . "modules/" . $folder . "/" . $folder . ".admin.php")){
				include_once(ABSPATH . "modules/" . $folder . "/" . $folder . ".admin.php");
			}
			
			$name = $module . "Admin";
			
			if(class_exists($name)){
				$this->modules[$module] = new $name($this->lncln);
			}
			else{
				continue;
			}
			
			$name = $this->get_module_name($module);
			$this->actions[$name] = $this->modules[$module]->actions();
		}
	}
	
	/**
	 * Check to see if requested module is indeed a loaded module
	 * @since 0.13.0
	 */
	protected function check_module($module_check){
		foreach($this->info as $info){
			if($info['name'] == $module_check && isset($this->modules[$info['class']])){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Check to see if a module has the action requested
	 * @since 0.13.0
	 */
	protected function check_action($module, $action){
		$class = $this->get_module_class($module);
		
		foreach($this->actions[$module]['urls'] as $group){
			foreach($group as $url => $name){
				if($url == $action && method_exists($this->modules[$class], $action)){
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Get the class of module based on its name
	 * @since 0.13.0
	 * 
	 * @param $module String Module's name
	 * 
	 * @return String Module's class
	 */
	protected function get_module_class($module){
		foreach($this->info as $info){
			if($info['name'] == $module){
				return $info['class'];
			}
		}
		
		return "";
	}
	
	/**
	 * Get the name of module based on its class
	 * @since 0.13.0
	 * 
	 * @param $module String Module's class
	 * 
	 * @return String Module's name
	 */
	protected function get_module_name($module){
		foreach($this->info as $info){
			if($info['class'] == $module){
				return $info['name'];
			}
		}
		
		return "";
	}
	
	/**
	 * Landing page for the admin panel
	 * Shows all of the quick links for modules
	 * @since 0.13.0
	 */
	protected function show_index(){
		echo "Welcome to the Admin panel<br />";
		
		ksort($this->actions);
		
		//Scan through the actions
		foreach($this->actions as $key => $module){		
			echo "<div class='admin_quick_links'>";
			echo "<span class='admin_link'>" . $key . "</span> (<a href='" . URL ."admin/" . $key . "'>More actions</a>)<br />";	
			//Look through the 'urls' section for the different ones
			foreach($module['urls'] as $group){
				//Split the groups up
				foreach($group as $url => $name){
					//See if they match one of the quick links
					foreach($module['quick'] as $quick){
						if($quick == $url){
							echo "<a href='" . URL . "admin/" . $key . "/" . $url ."/'>" . $name . "</a><br />";
						}
					}
				}
			}
			echo "</div>";
		}
		
		echo "<div style='clear: both;'></div>";
	}
}
