<?
/**
 * class.php
 * 
 * Contains the interface for modules
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

class Ratings implements Module{
	public $name = "Ratings";
	public $displayName = "Rating";
	
	/**
	 * Construct to pass the reference of lncln so that modules 
	 * can access permissions and settings
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $lncln lncln Main class variable
	 */
	public function __construct(&$lncln){
		$this->lncln = $lncln;
	}
	
	/**
	 * Called if the Module has it's own page
	 * Such as albums or search
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function index(){
		$this->lncln->display->message("This module does not have an associated page");
	}
	
	/**
	 * Called after a successful upload
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int ID of new image
	 * @param $data array Extra material needed, tag information, etc
	 */
	public function add($id, $data){
		
	}
	
	/**
	 * Edits an image with the data provided
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int ID of image
	 * @param $data array Extra material needed, tag information, etc
	 */	
	public function edit($id, $data){
		
	}
	
	/**
	 * Called during the upload screen. Contains the form information needed,
	 * will be passed to add() after successful upload
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function upload(){
		return "";
	}
	
	/**
	 * Creates the form information needed during moderation
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int Image to gather information about and populate the input
	 */
	public function moderate($id){
		return "";
	}
	
	/**
	 * Creates the link in the header
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function headerLink(){
		return "";
	}
	
	/**
	 * Creates the icon underneath images
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function icon($id){
		if($this->lncln->user->permissions['rate'] == 1):
			$output = "
			<a href='$action&amp;rateUp=$id'><img src='" .URL ."theme/" .THEME ."/images/up.png' alt='Up' title='Up' style='border: none;'/></a>
			<a href='$action&amp;rateDown=$id'><img src='" .URL ."theme/" .THEME ."/images/down.png' alt='Down' title='Down' style='border: none;'/></a>";
		endif;
		
		return $output;
	}
	
	/**
	 * Creates text above the image.  Text only
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function aboveImage($id, $action){
		
	}
	
	/**
	 * Creates text underneath the image.  May contain a form
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function underImage($id, $action){
		return "";
	}
}
?>
