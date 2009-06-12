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
class Report extends Module{
	
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
		
		$query = array(
			'type' => 'UPDATE',
			'table' => 'images',
			'set' => array(
				'report' => '!`report` + ' . $this->lncln->user->permissions['reportValue'],
				),
			'where' => array(
				array(
					'field' => 'id',
					'compare' => '=',
					'value' => $image,
					),
				),
			'limit' => array(1),
			);
		
		$this->db->query($query);
		
		if($this->db->affected_rows() == 1){
			$query = array(
				'type' => 'SELECT',
				'fields' => array('report'),
				'table' => 'images',
				'where' => array(
					array(
						'field' => 'id',
						'compare' => '=',
						'value' => $image,
						),
					),
				);
			
			$this->db->query($query);
			
			$row = $this->db->fetch_one();
			
			if($row['report'] >= 5){
				$query = array(
					'type' => 'UPDATE',
					'table' => 'images',
					'set' => array(
						'queue' => 1,
						),
					'where' => array(
						array(
							'field' => 'id',
							'compare' => '=',
							'value' => $image, 
							),
						),
					'limit' => array(1),
					);
				
				$this->db->query($query);
			}
		}
		
		$this->lncln->display->message("Image #" . $image . " has been reported.  Thank you.");
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
		if($this->lncln->user->permissions['report'] == 1){
			echo "<a href='" . URL . "report/" . $id . "'><img src='" . URL . "theme/" . THEME . "/images/report.png' alt='Report Image' title='Report Image' style='border: none;'/></a>";
		}
	}
}