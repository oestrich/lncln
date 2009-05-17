<?php
/**
 * class.php
 * 
 * Obscene module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

class Obscene{
	public $name = "Obscene";
	public $displayName = "Obscene";
	
	public function __construct(&$lncln){
		$this->lncln = $lncln;
	}
	
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
	
	public function above($id){
		if($this->lncln->type == "thumb"){
			return "";
		}
		
		$sql = "SELECT obscene FROM images WHERE id = " . $id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		$obscene = $row['obscene'] == 1 ? "<div id='vob" . $id . "'>This image is obscene</div>" : "";
				
		return $obscene;
	}
		
	public function header_link(){
		if($this->lncln->user->isUser == false){
			$url = $_COOKIE['obscene'] == 1 ? 'off' : 'on';
			$status = $_COOKIE['obscene'] == 1 ? "On" : "Off";
			echo "<a href='" . URL . "obscene/view/" . $url . "'>View Obscene</a> (" . $status . ")";
		}	
	}
	
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
	
	public function add($id, $data){
		$this->edit($id, $data);
	}
	
	/**
	 * Obscenes images.  Just flips the images obscene number
	 * @since 0.5.0
	 * 
	 * @param $id int ID of image
	 * @param $data array Extra material needed, tag information, etc
	 */	
	public function edit($id, $data){		
		$sql = "SELECT type, obscene FROM images WHERE id = " . $id;
		$result = mysql_query($sql);
		
		if($data[0] == ""){
			$data[0] = $data[1];
		}
		
		if(mysql_num_rows($result) == 1){
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
		
		$sql = "UPDATE images SET obscene = " . $num . " WHERE id = " . $id;
		mysql_query($sql);
	}
	
	public function icon($id){
		if($this->lncln->user->permissions['obscene'] == 1){
			$sql = "SELECT obscene FROM images WHERE id = " . $id;
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			$obscene = $row['obscene'] == 1 ? "false" : "true";
			
		 	return "<a href='" . URL . "action/obscene/" . $obscene . "/" . $id ."'>" .
		 		"<img src='" . URL . "theme/" . THEME . "/images/obscene.png' alt='Obscene' title='Obscene' style='border: none;'/></a>";
		}
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
		$sql = "SELECT obscene FROM images WHERE id = " . $id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['obscene'] == 1 && ($_COOKIE['obscene'] == 0 || !isset($_COOKIE['obscene']))){
			return true;
		}
		else{
			return false;
		}
	}
}
