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
		if($this->lncln->user->permissions['ratings'] == 0)
			return "You cannot rate";		
		
		if($data[1] != "down" && $data[1] != "up")
			return "No need to rate now";
		
		$rating = $this->lncln->user->permissions['ratingsValue'];
		
		if($data[1] == "down")
			$rating = $rating * -1;
		
		$sql = "SELECT rating FROM ratings WHERE picId = " . $id . " AND userId = " . $this->lncln->user->userID;
		$result = mysql_query($sql);
		$numRows = mysql_num_rows($result);
		
		if($numRows > 0){
			$row = mysql_fetch_assoc($result);
		}
		
		if($numRows == 1 && $row['rating'] == $rating){
			return "You already rated it";
		}
		elseif(($numRows == 1 && $row['upDown'] != $rating) || $numRows == 0){
			if(isset($row['rating']) && $row['rating'] != $rating){
				$sql = "DELETE FROM ratings WHERE picID = " . $id . " AND userID = " . $this->lncln->user->userID;
			}
			else{
				$sql = "INSERT INTO ratings (picID, userId, rating) VALUES (" . $id . ", " . $this->lncln->user->userID . ", " . $rating . ")";
			}
			
			mysql_query($sql);
			
			$sql = "SELECT SUM(rating) FROM ratings WHERE picId = " . $id;
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			if($row['SUM(rating)'] == null){
				$row['SUM(rating)'] = 0;
			}
			
			if($row['SUM(rating)'] < -10){
				$small = ", small = 1 ";
			}
			
			$sql = "UPDATE images SET rating = " . $row['SUM(rating)'] . $small . " WHERE id = " . $id . " LIMIT 1";
			mysql_query($sql);
			
			return "Rated successfully";
		}
		elseif($numRows > 0){
			return "You already rated it";
		}
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
	public function icon($id, $action){
		if($this->lncln->user->permissions['ratings'] == 1):
			$output = "
			<a href='$action&amp;action=ratings&amp;subAction=up&amp;id=$id'><img src='" .URL ."theme/" .THEME ."/images/up.png' alt='Up' title='Up' style='border: none;'/></a>
			<a href='$action&amp;action=ratings&amp;subAction=down&amp;id=$id'><img src='" .URL ."theme/" .THEME ."/images/down.png' alt='Down' title='Down' style='border: none;'/></a>";
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
		return "Rating: " . $this->getRating($id);
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
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * 
	 */
	private function getRating($id){
		if(!is_numeric($id))
			return 0;
		
		$sql = "SELECT rating FROM images WHERE id = " . $id;
		$result = mysql_query($sql);
		
		if(mysql_num_rows($result) < 1){
			return 0;
		}
		
		$row = mysql_fetch_assoc($result);
		
		return $row['rating'];
	}
}
?>
