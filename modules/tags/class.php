<?
/**
 * module.php
 * 
 * Contains the interface for modules
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

class Tags implements Module{
	public $name = "Tags"; //Name printed out in forms
	
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
		$this->edit($id, $data);
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
		$id = stripslashes($id);
		$id = mysql_real_escape_string($id);
		
		$tags = split(',', $data[0]);
		$tags = array_map('trim', $tags);
		$tags = array_map('prepareSQL', $tags);
		
		$sql = "DELETE FROM tags WHERE picId = " . $id;
		mysql_query($sql);
		
		$sql = "INSERT INTO tags (picId, tag) VALUES ";
		
		foreach($tags as $tag){
			if($tag == ""){
				continue;
			}
			$sql .= "(" . $id . ", '" . $tag . "'), ";
		}
	
		$sql = substr_replace($sql ,"",-2);
		
		mysql_query($sql);
	}
	
	/**
	 * Called during the upload screen. Contains the form information needed,
	 * will be passed to add() after successful upload
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @return array Keys: type, name, value
	 */
	public function upload(){
		return array("type" => "text", "name" => "tags", "value" => "");
	}
	
	/**
	 * Creates the form information needed during moderation
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function moderate($id){
		return array("type" => "text", "name" => "tags", "value" => $this->getTags($id, true));
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
		
	}
	
	/**
	 * Creates text underneath the image.  May contain a form
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function underImage($id){
		return "Tags: " . $this->getTags($id);
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */

	/**
	 * Gathers tags from an image together, string or array form
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int Image id
	 * @param $string bool String if true, array if false
	 * 
	 * @return mixed Array of tags or string joined by ','
	 */
	private function getTags($id, $string = false){
		$sql = "SELECT tag FROM tags WHERE picId = " . $id;
		$result = mysql_query($sql);
		
		$tags = array();
		
		while($row = mysql_fetch_assoc($result)){
			$tags[] = $row['tag'];
		}
		
		if($string == false)
			return $tags;
		
		$tags = join(', ', $tags);
		
		return $tags;
	}
}
?>
