<?php
/**
 * class.php
 * 
 * Main class for the queue module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Main class for the Queue module
 * @since 0.13.0
 * 
 * @package lncln
 */
class Queue{
	/**
	 * @var string Name of module
	 */
	public $name = "Queue";
	
	/**
	 * @var string Display name of module
	 */
	public $displayName = "Queue";
	
	/**
	 * Construct to pass the reference of lncln so that modules 
	 * can access permissions and settings
	 * @since 0.13.0
	 * 
	 * @param lncln &$lncln Main class variable
	 */
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
	 * @return string Link
	 */
	public function header_link(){
		if($this->lncln->user->permissions['isAdmin']){
			return "<a href='" . URL . "queue/'>Check the Queue (" . $this->check_queue() . ")</a>";
		}
		
		return "";
	}
	
	/**
	 * Alters the query called by get_data()
	 * @since 0.13.0
	 * 
	 * @return array Keys: 'field', 'compare', 'value'
	 */
	public function get_data_sql(){
		return array(
			'field' => 'queue',
			'compare' => '=',
			'value' => 0,
			);
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Number of images in the queue
	 * @since 0.13.0
	 * 
	 * @return int Total number of images in the queue
	 */
	protected function check_queue(){
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
		return $row['COUNT(*)'];
	}
}