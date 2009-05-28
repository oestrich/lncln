<?php
/**
 * class.display.php
 * 
 * Display class
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Display class.  Manages settings
 * @since 0.11.0
 * 
 * @package lncln
 */
class Display{
	public $db = null;
	
	public $settings = array();
	public $lncln;
	
	public $rows;
	public $title;
	
	/**
	 * Sets up the settings array
	 * @since 0.11.0
	 */
	function __construct(&$lncln){
		global $db;
		$this->db = $db;
		
		$this->lncln = $lncln;
		
		$sql = "SELECT * FROM settings";
		$this->db->query($sql);
		$results = $this->db->fetch_all();
				
		foreach($results as $row){
			$this->settings[$row['name']] = $row['value'];
		}
		
		define("THEME", $this->settings['theme']);
		
		$this->set_title();
	}
	
	/**
	 * includes a file from include folder
	 * So that you can include files that require a $lncln variable from a module
	 * @since 0.13.0
	 */
	function includeFile($includeFile){
		$lncln = $this->lncln;
		
		include_once(ABSPATH . "includes/" . $includeFile);
	}
	
	/**
	 * Includes module related css
	 * @since 0.13.0
	 */
	function include_css(){
		$output = "";
		
		foreach($this->lncln->modules as $module){
			$name = strtolower($module->name);
			$file = "modules/" . $name . "/" . $name . ".css";
			
			if(file_exists(ABSPATH . $file)){
				$output .= "<link type='text/css' rel='stylesheet' href='" . URL . $file . "' />\n";
			}
		}
		
		return $output;
	}
	
	/**
	 * Sets the title for current page
	 * @since 0.13.0
	 * 
	 * @param $title String title for current page
	 */
	function set_title($title = ""){
		if($title == ""){
			$this->title = $this->settings['title'];
			return 1;
		}
		
		$this->title = $title . " - " . $this->settings['title'];
	}

	
	/**
	 * Show only the message given on the screen.
	 * Useful for "Please login"  or "Not allowed"
	 * Exits upon completion
	 * @since 0.12.0
	 * 
	 * @param $msg String Message to be shown
	 */
	function message($msg){
		$lncln = $this->lncln;
		
		$this->show_header();
		
		echo $msg;
		
		$this->show_footer();
		exit();
	}
	
	/**
	 * Shows the header for the current theme.
	 * @since 0.13.0
	 */
	function show_header(){
		$lncln = $this->lncln;
		
		include_once(ABSPATH . "theme/" . THEME . "/header.php");
	}
	
	/**
	 * Shows the footer for current theme
	 * @since 0.13.0
	 */
	function show_footer(){
		$lncln = $this->lncln;
		
		include_once(ABSPATH . "theme/" . THEME . "/footer.php");
	}
	
	/**
	 * Prints out posts
	 * @since 0.13.0
	 */
	function show_posts(){
		$lncln = $this->lncln;
		
		echo $this->lncln->prevNext();
		
		if($lncln->page == 0 && $lncln->maxPage == 0){
			echo "No images.<br />";
		}
		
		foreach($lncln->images as $image){
			include(ABSPATH . "theme/" . THEME . "/post.php");
		}
		
		echo $this->lncln->prevNext(true);
	}
}
