<?
/**
 * class.php
 * 
 * Ratings module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

class Ratings{
	public $name = "Ratings";
	public $displayName = "Rating";
	
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
	 * Edits an image with the data provided
	 * @since 0.13.0
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
						
			$sql = "UPDATE images SET rating = " . $row['SUM(rating)'] . " WHERE id = " . $id . " LIMIT 1";
			mysql_query($sql);
			
			return "Rated successfully";
		}
		elseif($numRows > 0){
			return "You already rated it";
		}
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
		if($this->lncln->user->permissions['ratings'] == 1):
			$output = "\t\t<a href='" .URL . "action/ratings/up/$id'><img src='" .URL . "theme/" .THEME ."/images/up.png' alt='Up' title='Up' style='border: none;'/></a>
		<a href='" .URL . "action/ratings/down/$id'><img src='" .URL . "theme/" .THEME ."/images/down.png' alt='Down' title='Down' style='border: none;'/></a>\n";
		endif;
		
		return $output;
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
		return "Rating: " . $this->getRating($id);
	}
	
	/**
	 * Checks to see if an image needs to be shrunk
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return bool True: small
	 */
	public function small($id){
		$sql = "SELECT rating FROM images WHERE id = " . $id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		$small = $row['rating'] < -10 ? 1 : 0;
		
		return $small;
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Get the rating for an image
	 * @since 0.13.0
	 * 
	 * @param $id int Image id
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
