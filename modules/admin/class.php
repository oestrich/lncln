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
	
	public $db = null;
	
	/**
	 * Construct to pass the reference of lncln so that modules 
	 * can access permissions and settings
	 * @since 0.13.0
	 * 
	 * @param $lncln lncln Main class variable
	 */
	public function __construct(&$lncln){
		$this->db = get_db();
		
		$this->lncln = $lncln;
	}
	
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
					
					foreach($this->actions[$module]['urls'] as $url => $name){
						if($name != ""){
							echo "<a href='" . URL . "admin/" . $module . "/$url'>" . $name . "</a><br />\n";
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
							"<span class='admin_link'><a href='" . URL . "admin/" .
							$module . "'>". $module .
							"</a></span><span class='admin_action'> - " . ucwords($action) . "</span><br />\n<br />\n";

					//This is because modules are loaded under the class name
					$this->modules[$this->get_module_class($module)]->$action();
					
					$name = $this->actions[$module]['urls'][$action];
					
					$this->lncln->display->set_title($name . " - " . $module);
				}
				else{
					echo "Not an action";
				}
			}
		}
		
		if($this->lncln->params[0] == ""){
			$this->show_info();
		}
		
		//This allows for the title to change while in the admin panel
		$content = ob_get_contents();
		ob_end_clean();		
		
		$this->lncln->display->includeFile("header.php");
		
		echo $content;
		
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
				$info = $name();
				$this->info[$info['name']] = $info;
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
	
	/**
	 * Will be moved to it's own module eventaully
	 * @since 0.13.0
	 */
	protected function check_queue(){
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
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
			if(file_exists(ABSPATH . "modules/" . strtolower($module) . "/admin.php")){
				include_once(ABSPATH . "modules/" . strtolower($module) . "/admin.php");
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
		
		foreach($this->actions[$module]['urls'] as $url => $name){
			if($url == $action && method_exists($this->modules[$class], $action)){
				return true;
			}
		}
		
		return false;
	}
	
	protected function get_module_class($module){
		foreach($this->info as $info){
			if($info['name'] == $module){
				return $info['class'];
			}
		}
		
		return "";
	}
	
	protected function get_module_name($module){
		foreach($this->info as $info){
			if($info['class'] == $module){
				return $info['name'];
			}
		}
		
		return "";
	}
}
