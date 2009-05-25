<?php
/**
 * class.php
 * 
 * Main class for the queue module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

class Queue{
	public $name = "Queue";
	
	public function __construct(&$lncln){
		$this->db = get_db();
		
		$this->lncln = $lncln;
	}
	
	/**
	 * Called if the Module has it's own page
	 * @since 0.13.0
	 */
	public function index(){
		echo "Welcome to the queue";
	}
	
	/**
	 * Creates the link in the header
	 * @since 0.13.0
	 *
	 * @return string Link or form
	 */
	public function header_link(){
		if($this->lncln->user->permissions['isAdmin']){
			return "<a href='" . URL . "queue/'>Check the Queue (" . $this->check_queue() . ")</a>";
		}
		
		return "";
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * In it's own module
	 * @since 0.13.0
	 */
	protected function check_queue(){
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
		return $row['COUNT(*)'];
	}
}
