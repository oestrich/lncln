<?
/**
 * class.php
 * 
 * Ratings module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Main class for the Ratings module
 * @since 0.13.0
 * 
 * @package lncln
 */
class Ratings extends Module{
	/**
	 * @var string Name of Module
	 */
	public $name = "Ratings";
	
	/**
	 * @var string Display name of Module
	 */
	public $displayName = "Rating";
	
	/**
	 * Edits an image with the data provided
	 * @since 0.13.0
	 * 
	 * @param int $id ID of image
	 * @param array $data Extra material needed, tag information, etc
	 * 
	 * @return string Completion message
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
		$this->db->query($sql);
		$numRows = $this->db->num_rows();
		
		if($numRows > 0){
			$row = $this->db->fetch_one();
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
			
			$this->db->query($sql);
			
            $sql = "SELECT SUM(rating) FROM ratings WHERE picId = " . $id;
            $this->db->query($sql);
            $row = $this->db->fetch_one();
            
            if($row['SUM(rating)'] == null){
                $row['SUM(rating)'] = 0;
            }
			
			$sql = "UPDATE images SET rating = " . $row['SUM(rating)'] . " WHERE id = " . $id ;
			$this->db->query($sql);
			
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
	 * @param int $id Image ID
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
	 * @param int $id Image ID
	 * 
	 * @return string Text above the image
	 */
	public function above($id){
		return "Rating: " . $this->get_rating($id);
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
		$row = $this->get_rating($id);
		
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
	 * @param int $id Image id
	 * 
	 * @return int Rating for Image
	 */
	private function get_rating($id){
		if(!is_numeric($id))
			return 0;
		
		foreach($this->lncln->images as $image){
			if($image['id'] == $id){
				return $image['rating'];
			}
		}
		
		return 0;
	}
}
