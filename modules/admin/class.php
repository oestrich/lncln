<?php
/**
 * class.php
 * 
 * Main admin module class
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

class Admin{
	public $name = "Admin";
	public $displayName = "Admin";
	public $info = array();
	protected $modules = array();
	protected $actions = array();
	
	/**
	 * Construct to pass the reference of lncln so that modules 
	 * can access permissions and settings
	 * @since 0.13.0
	 * 
	 * @param $lncln lncln Main class variable
	 */
	public function __construct(&$lncln){
		$this->lncln = $lncln;
	}
	
	/**
	 * Called if the Module has it's own page
	 * Such as albums or search
	 * @since 0.13.0
	 */
	public function index(){
		$this->check_admin();
		
		$this->load_admin_modules();
		
		$this->lncln->display->includeFile("header.php");
		
		//If going to a module page, enter
		if($this->lncln->params[0] != ""){
			//If just the top module page, show actions
			if($this->lncln->params[1] == ""){
				if($this->check_module($this->lncln->params[0])){
					echo "<span class='admin_link'>". $this->lncln->params[0] . "</span><br /><br />\n";
					
					foreach($this->actions[$this->lncln->params[0]]['urls'] as $url => $name){
						if($name != ""){
							echo "<a href='" . URL . "admin/" . $this->lncln->params[0] . "/$url'>" . $name . "</a><br />\n";
						}
					}
				}
				else{
					echo "Not a module";
				}
			}
			else{
				//Only do the correct action!
				if($this->check_action($this->lncln->params[0], $this->lncln->params[1])){
					echo "<span class='admin_link'><a href='" . URL . "admin/" .
							$this->lncln->params[0] . "'>". $this->lncln->params[0] .
							"</a></span><span class='admin_action'> - " . ucwords($this->lncln->params[1]) . "</span><br />\n<br />\n";
					
					$action = $this->lncln->params[1];
					$this->modules[$this->lncln->params[0]]->$action();
				}
				else{
					echo "Not an action";
				}
			}
		}
		
		if($this->lncln->params[0] == ""){
			$this->load_info();
			$this->show_info();
		}
		
		$this->lncln->display->includeFile("footer.php");
	}
	
	/**
	 * Creates the link in the header
	 * @since 0.13.0
	 *
	 * @return string Link or form
	 */
	public function header_link(){
		if($this->lncln->user->permissions['isAdmin']){
			return "Admin: <a href='" . URL . "admin/'>Admin Panel</a> " .
				"<a href='" . URL . "admin/queue.php'>Check the Queue (" . $this->check_queue() . ")</a>";
		}
		
		return "";
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
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
				$this->info[] = $name();
			}
			else{
				continue;
			}
		}
	}
	
	/**
	 * Prints out the info for modules
	 * @since 0.13.0
	 */
	protected function show_info(){
		foreach($this->info as $info){
			$requires = join(" ", $info['requires']);
			
			if($requires == ""){
				$requires = "No requirements";
			}
			
			echo "<span class='admin_link'><a href='" . URL . "admin/" . $info['name']. "/'>" . $info['name'] . "</a></span>\n";
			echo "<p class='admin_description'>" . $info['description'] . "</p>\n";
			echo "<table>\n";
			echo "<tr><td>Version:</td><td>" . $info['version'] . "</td></tr>\n";
			echo "<tr><td>Package:</td><td>" . $info['package'] . "</td></tr>\n";
			echo "<tr><td>Requires:</td><td>" . $requires . "</td></tr>\n";
			echo "</table>\n";
			echo "<br />";
		}
	}
	
	/**
	 * Will be moved to it's own module eventaully
	 * @since 0.13.0
	 */
	protected function check_queue(){
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		return $row['COUNT(*)'];
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
		foreach($this->lncln->modules_enabled as $module){
			$name = $module . "Admin";
			
			if(class_exists($name)){
				$this->modules[$module] = new $name($this->lncln);
			}
			else{
				continue;
			}
			
			$this->actions[$module] = $this->modules[$module]->actions();
		}
	}
	
	/**
	 * Check to see if requested module is indeed a loaded module
	 * @since 0.13.0
	 */
	protected function check_module($module_check){
		foreach($this->modules as $module){
			if($module->name == $module_check){
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
		foreach($this->actions[$module]['urls'] as $url => $name){
			if($url == $action){
				return true;
			}
		}
		
		return false;
	}
}
