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

class Admin implements Module{
	public $name = "Admin";
	public $displayName = "Admin";
	
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
		$this->lncln->display->includeFile("header.php");
		
		$this->load_info();
		
		$this->lncln->display->includeFile("footer.php");
	}
	
	/**
	 * Called after a successful upload
	 * @since 0.13.0
	 * 
	 * @param $id int ID of new image
	 * @param $data array Extra material needed, tag information, etc
	 */
	public function add($id, $data){
		
	}
	
	/**
	 * Edits an image with the data provided
	 * @since 0.13.0
	 * 
	 * @param $id int ID of image
	 * @param $data array Extra material needed, tag information, etc
	 */	
	public function edit($id, $data){
		
	}
	
	/**
	 * Called during the upload screen. Contains the form information needed,
	 * will be passed to add() after successful upload
	 * @since 0.13.0
	 *
	 * @return array Keys: type, name, value, options
	 */
	public function upload(){
		
	}
	
	/**
	 * Creates the form information needed during moderation
	 * @since 0.13.0
	 * 
	 * @param $id int Image to gather information about and populate the input
	 *
	 * @return array Keys: type, name, value, options
	 */
	public function moderate($id){
		
	}
	
	/**
	 * Creates the link in the header
	 * @since 0.13.0
	 *
	 * @return string Link or form
	 */
	public function header_link(){
		
	}
	
	/**
	 * Creates the icon underneath images
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * @param $action array Action for the icon
	 * 
	 * @return string Icon underneath the image
	 */
	public function icon($id, $action){
		
	}
	
	/**
	 * Creates text above the image.  Text only
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * @param $action array Action for the form
	 * 
	 * @return string Text above the image
	 */
	public function above($id, $action){
		
	}
	
	/**
	 * Creates text below the image.  May contain a form
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * @param $action array Action for the form
	 * 
	 * @return string Text underneath the image
	 */
	public function below($id, $action){
		
	}
	
	/**
	 * Pushes content out via the RSS feed
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return string Output for the RSS feed
	 */
	public function rss($id){
		
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
			}
			else{
				continue;
			}
			
			$requires = join(" ", $info['requires']);
			
			if($requires == ""){
				$requires = "No requirements";
			}
			
			echo "<span class='admin_link'>" . $info['name'] . "</span>\n";
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
