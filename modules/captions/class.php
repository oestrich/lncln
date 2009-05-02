<?
/**
 * class.php
 * 
 * Contains the interface for modules
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

class Captions implements Module{	
	public $name = "Captions";
	
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
		return array();
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
		return array();
	}
	
	/**
	 * Creates the link in the header
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function headerLink(){
		
	}
	
	/**
	 * Creates the icon underneath images
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function icon($id){
		return "";
	}
	
	/**
	 * Creates text underneath the image.  May contain a form
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function underImage($id, $action){
		//caption stuff
		if($this->lncln->user->permissions['caption'] == 1){
			$onClick = "onclick=\"showModule('" . $this->name . "', '" . $id . "');\"";
			$class = "class='underImage'";
		}
		else{
			$onClick = "";
			$class = "";
		}
		
		$output = "
			<div id='captions$id' " . $onClick . $class . ">
					" . $this->getCaption($id) . "
			</div>";
		
		if($this->lncln->user->permissions['captions'] == 1){
			$output .= "
			<form id='c$id' style='display: none;' enctype='multipart/form-data' action='$action&amp;caption=true' method='post'>
				<input type='hidden' name='id' value='$id' />
				<textarea name='caption' rows='6' cols='40' id='formCaption$id'>" . $this->getCaption($id, false) . "</textarea>
				<input type='submit' value='Caption!' />
			</form>";
		}
			
		return $output;
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Get an image's caption
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int Image id
	 */
	private function getCaption($id, $noCaption = true){
		if(!is_numeric($id))
			return "Bad id";
		
		$sql = "SELECT caption FROM images WHERE id = " . $id;
		$result = mysql_query($sql);
		
		if(mysql_num_rows($result) < 1)
			return "No such image";
			
		$row = mysql_fetch_assoc($result);
		
		if($row['caption'] == "" && $noCaption == true)
			return "No caption.";
		
		return $row['caption'];
	}
}

?>
