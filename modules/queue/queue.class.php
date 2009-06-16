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
class Queue extends Module{
	/**
	 * @var string Name of module
	 */
	public $name = "Queue";
	
	/**
	 * @var string Display name of module
	 */
	public $displayName = "Queue";
	
	/**
	 * @var bool If the queue is being accessed
	 */
	public $in_queue = false;
	
	/**
	 * Called if the Module has it's own page
	 * @since 0.13.0
	 */
	public function index(){
		if($this->lncln->user->permissions['isAdmin'] == 1){
			echo "Welcome to the queue.  Please view the queue from the <a href='" . URL . "admin/Queue/manage'>admin panel</a>.";
		}
		else{
			echo "You must be an admin to view this area.";
		}
	}
	
	/**
	 * Creates the link in the header
	 * @since 0.13.0
	 *
	 * @return string Link
	 */
	public function header_link(){
		if($this->lncln->user->permissions['isAdmin']){
			return "<a href='" . URL . "admin/Queue/manage'>Check the Queue (" . $this->check_queue() . ")</a>";
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
		if($this->in_queue == false){
			return array(
				'field' => 'queue',
				'compare' => '=',
				'value' => 0,
				);
		}
		
		return array();
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
		$query = array(
			'type' => 'SELECT',
			'fields' => array('!COUNT(id)'),
			'table' => 'images',
			'where' => array(
				array(
					'field' => 'queue',
					'compare' => '=',
					'value' => 1,
					),
				),
			);
		
		$this->db->query($query);
		$row = $this->db->fetch_one();
		
		return $row['COUNT(id)'];
	}
}
