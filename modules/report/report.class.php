<?php
/**
 * class.php
 * 
 * Main class for Report module
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
class Report {
	
	/**
	 * Construct
	 * @since 0.13.0
	 */
	public function __construct(&$lncln){
		$this->db = get_db();
		
		$this->lncln = $lncln;
	}
	
	/**
	 * Main page of reporting
	 * @since 0.13.0
	 */
	public function index(){
		if($this->lncln->user->permissions['report'] == 0){
			$this->lncln->display->message("You can't report images");
		}
		if($this->lncln->params[0] == "" || !isset($this->lncln->params[0])){
			header("location:" . URL . "index/");
			exit();
		}
		
		$image = $this->db->prep_sql($this->lncln->params[0]);
		
		$sql = "UPDATE images SET report = report + " . $this->lncln->user->permissions['reportValue'] . " WHERE id = " . $image . " LIMIT 1";
		$this->db->query($sql);
		
		if($this->db->affected_rows() == 1){
			$sql = "SELECT report FROM images WHERE id = " . $image;
			$this->db->query($sql);
			
			$row = $this->db->fetch_one();
			
			if($row['report'] >= 5){
				$sql = "UPDATE images SET queue = 1 WHERE id = " . $image . " LIMIT 1";
				$this->db->query($sql);
			}
		}
		
		$this->lncln->display->message("Image #" . $image . " has been reported.  Thank you.");
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
		if($this->lncln->user->permissions['report'] == 1){
			echo "<a href='" . URL . "report/" . $id . "'><img src='" . URL . "theme/" . THEME . "/images/report.png' alt='Report Image' title='Report Image' style='border: none;'/></a>";
		}
	}
}