<?php
/**
 * module.php
 * 
 * Contains base classes for modules
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Module base class
 * Contains everything that a module can do, aka the module API
 * @since 0.13.0
 * 
 * @package lncln
 */
class ModuleModel extends Module{	
	/**
	 * Called if the Module has it's own page
	 * Such as albums or search
	 * @since 0.13.0
	 */
	public function index(){
		
	}
	
	/**
	 * Called after a successful upload
	 * @since 0.13.0
	 * 
	 * @param int $id ID of new image
	 * @param array $data Extra material needed, tag information, etc
	 */
	public function add($id, $data){
		
	}
	
	/**
	 * Edits an image with the data provided
	 * @since 0.13.0
	 * 
	 * @param int $id ID of image
	 * @param array $data Extra material needed, tag information, etc
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
	 * @param int $id Image to gather information about and populate the input
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
	 * @param int $id Image ID
	 * 
	 * @return string Icon underneath the image
	 */
	public function icon($id){
		
	}
	
	/**
	 * Creates text above the image.  Text only
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return string Text above the image
	 */
	public function above($id){
		
	}
	
	/**
	 * Creates text below the image.  May contain a form
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return string Text underneath the image
	 */
	public function below($id){
		
	}
	
	/**
	 * Pushes content out via the RSS feed
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return string Output for the RSS feed
	 */
	public function rss($id){
		
	}
	
	/**
	 * Checks to see if an image needs to be shrunk
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return bool True: small
	 */
	public function small($id){
		
	}
	
	/**
	 * Alters the query called by get_data()
	 * @since 0.13.0
	 * 
	 * @return array Keys: 'field', 'compare', 'value'
	 */
	public function get_data_sql(){
		
	}
	
	/**
	 * Output during the <head> section of html
	 * @since 0.13.0
	 * 
	 * @return string html output
	 */
	public function html_head(){
		
	}
}

/**
 * Admin module API
 * @since 0.13.0
 * 
 * @package lncln
 */
class AdminModel extends ModuleModel{
	/**
	 * Registers actions that will be used in the admin panel
	 * @since 0.13.0
	 * 
	 * @return array Keys: url 
	 */
	public function actions(){
		
	}
}
