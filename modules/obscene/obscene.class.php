<?php
/**
 * class.php
 * 
 * Obscene module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Main class for lncln
 * @since 0.13.0
 * 
 * @package lncln
 */
class Obscene extends Module{
	/**
	 * @var string Name for module
	 */
	public $name = "Obscene";
	
	/**
	 * @var string Display name for module
	 */
	public $displayName = "Obscene";

	/**
	 * @var array Cache of values from database
	 */
	public $values = array();

	/**
	 * Changes the obscene cookie, flips it
	 * @since 0.13.0
	 */
	public function index(){
		if($this->lncln->params[0] == "view"){
			if($this->lncln->params[1] == "on"){
				setcookie('obscene', 1, time() + (60 * 60 * 24), URL);
			}
			else{
				setcookie('obscene', 0, time() + (60 * 60 * 24), URL);
			}
			header("location:" . URL . "index/");	
			exit();
		}
		else{
			header("location:" . URL . "index/");
			exit();
		}
	}
	
	/**
	 * Message above the image
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return string Message if obscene
	 */
	public function above($id){
		if($this->lncln->type == "thumb"){
			return "";
		}
		
		$row = $this->get_obscene($id);
		
		$obscene = $row['obscene'] == 1 ? "<div id='vob" . $id . "'>This image is obscene</div>" : "";
				
		return $obscene;
	}
	
	/**
	 * Header link to change obscene
	 * @since 0.13.0
	 */
	public function header_link(){
		if($this->lncln->user->isUser == false){
			$url = $_COOKIE['obscene'] == 1 ? 'off' : 'on';
			$status = $_COOKIE['obscene'] == 1 ? "On" : "Off";
			echo "<a href='" . URL . "obscene/view/" . $url . "'>View Obscene</a> (" . $status . ")";
		}	
	}
	
	/**
	 * Called during the upload screen. Contains the form information needed,
	 * will be passed to add() after successful upload
	 * @since 0.13.0
	 *
	 * @return array Keys: type, name, value, options
	 */
	public function upload(){
		return array(
			"type" => "select", 
			"name" => "obscene", 
			"options" => array(
				array("id" => 0, "name" => "No"),
				array("id" => 1, "name" => "Yes"),
				),
			);
	}
	
	/**
	 * Creates the form information needed during moderation
	 * @since 0.13.0
	 * 
	 * @param int $id Image to gather information about and populate the input
	 *
	 * @return array Keys: type, name, value, options
	 */
	public function moderate($id){
		return array(
			"type" => "select", 
			"name" => "obscene", 
			"options" => array(
				array("id" => 0, "name" => "No"),
				array("id" => 1, "name" => "Yes"),
				),
			'value' => $this->small($id) ? "Yes" : "No",
			);
	}
	
	/**
	 * Called after a successful upload
	 * @since 0.13.0
	 * 
	 * @param int $id ID of new image
	 * @param array $data Extra material needed, tag information, etc
	 */
	public function add($id, $data){
		$this->edit($id, $data);
	}
	
	/**
	 * Obscenes images.  Just flips the images obscene number
	 * @since 0.5.0
	 * 
	 * @param int $id ID of image
	 * @param array $data Extra material needed, tag information, etc
	 */	
	public function edit($id, $data){		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('type', 'obscene'),
			'table' => 'images',
			'where' => array(
				array(
					'field' => 'id',
					'compare' => '=',
					'value' => $id,
					),
				),
			);
		
		$this->db->query($query);
		
		if($data[0] == ""){
			$data[0] = $data[1];
		}
		
		if($this->db->num_rows() == 1){
			if(!is_numeric($data[0])){
				$num = $data[0] == "true" ? 1 : 0;
			}
			else{
				$num = $data[0];	
			}
		}
		else{
			return "";
		}
		
		$query = array(
			'type' => 'UPDATE',
			'table' => 'images',
			'set' => array(
				'obscene' => $num,
				),
			'where' => array(
				array(
					'field' => 'id',
					'compare' => '=',
					'value' => $id,
					),
				),
			);
			
		$this->db->query($query);
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
		if($this->lncln->user->permissions['obscene'] == 1){
			$row = $this->get_obscene($id);
			
			$obscene = $row['obscene'] == 1 ? "false" : "true";
			
		 	return "<a href='" . URL . "action/obscene/" . $obscene . "/" . $id ."'>" .
		 		"<img src='" . URL . "theme/" . THEME . "/images/obscene.png' alt='Obscene' title='Obscene' style='border: none;'/></a>";
		}
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
		$row = $this->get_obscene($id);
		
		if($row['obscene'] == 1 && ($_COOKIE['obscene'] == 0 || !isset($_COOKIE['obscene']))){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function rss_keyword(){
		$keyword = array(
			array(
				'safe',
				array(
					'field' => 'obscene',
					'compare' => '=',
					'value' => 0,
					),
				),
			);
		
		return $keyword;
	}
	
	/**
	 * Returns the obscene status of an image
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return bool True if obscene
	 */
	private function get_obscene($id){
		foreach($this->lncln->images as $image){
			if($image['id'] == $id){
				return $image['obscene'];
			}
		}
		
		return 0;
	}
}
