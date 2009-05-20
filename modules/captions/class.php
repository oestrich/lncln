<?
/**
 * class.php
 * 
 * Captions module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

class Captions{	
	public $name = "Captions";
	public $displayName = "Caption";
	
	public $db = null;
	
	public $values = array();
	
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
		$this->lncln->display->message("This module does not have an associated page");
	}
	
	/**
	 * Called after a successful upload
	 * @since 0.13.0
	 * 
	 * @param $id int ID of new image
	 * @param $data array Extra material needed, tag information, etc
	 */
	public function add($id, $data){
		$this->edit($id, $data);
	}
	
	/**
	 * Edits an image with the data provided
	 * @since 0.13.0
	 * 
	 * @param $id int ID of image
	 * @param $data array Extra material needed, tag information, etc
	 */	
	public function edit($id, $data){
		$image = prepareSQL($id);
		$caption = prepareSQL($data[0]);
		
		$sql = "UPDATE images SET caption = '" . $caption . "' WHERE id = " . $id . " LIMIT 1";
		$this->db->query($sql);
	}
	
	/**
	 * Called during the upload screen. Contains the form information needed,
	 * will be passed to add() after successful upload
	 * @since 0.13.0
	 *
	 * @return array Keys: type, name, value, options
	 */
	public function upload(){
		return array("type" => "textarea", "name" => "captions", "value" => "");
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
		return array("type" => "textarea", "name" => "captions", "value" => $this->getCaption($id, false));
	}
	
	/**
	 * Creates the link in the header
	 * @since 0.13.0
	 *
	 * @return string Link or form
	 */
	public function header_link(){
		return "";
	}
	
	/**
	 * Creates the icon underneath images
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return string Icon underneath the image
	 */
	public function icon($id){
		return "";
	}
	
	/**
	 * Creates text above the image.  Text only
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return string Text above the image
	 */
	public function above($id){
		return "";
	}
	
	/**
	 * Creates text underneath the image.  May contain a form
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return string Text underneath the image
	 */
	public function below($id){
		//caption stuff
		if($this->lncln->user->permissions['captions'] == 1){
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
			<form id='c$id' style='display: none;' enctype='multipart/form-data' action='" . URL . "action/captions/$id' method='post'>
				<input type='hidden' name='id' value='$id' />
				<textarea name='captions' rows='6' cols='40' id='formCaptions$id'>" . $this->getCaption($id, false) . "</textarea>
				<input type='submit' value='Caption!' />
			</form>";
		}
		
		return $output;
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
		return $this->getCaption($id);
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Get an image's caption
	 * @since 0.13.0
	 * 
	 * @param $id int Image id
	 * @param $noCaption bool If true it will not return "No Caption."
	 * 
	 * @return string Caption for image
	 */
	private function getCaption($id, $noCaption = true){
		if(!is_numeric($id))
			return "Bad id";
		
		foreach($this->lncln->images as $image){
			if($image['id'] == $id){
				$caption = $image['caption'];
			}
		}
		
		if($caption == "" && $noCaption == true)
			return "No caption.";
		
		return $caption;
	}
}

?>
