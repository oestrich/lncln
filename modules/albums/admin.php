<?php
/**
 * admin.php
 * 
 * Administration class for Albums
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */


class AlbumsAdmin extends Albums{
	public function __construct(&$lncln){
		parent::__construct($lncln);
	}
	
	/**
	 * Add an album
	 * @since 0.13.0
	 */
	public function add(){
		if(isset($_POST['name'])){
			echo $this->addAlbum($_POST['name']);
		}
		
		$form = array(
			'action' => 'admin/Albums/add',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Add Album',
			);
		
		$form['inputs'][] = array(
			'title' => 'Add new Album',
			'type' => 'text',
			'name' => 'name',
			);
		
		echo create_form($form);
	}
	
	/**
	 * Manage your albums
	 * @since 0.13.0
	 */
	public function manage(){
		if($this->lncln->params[2] == "delete"){
			$this->deleteAlbum($this->lncln->params[3]);
			$this->lncln->display->message("Album deleted<br />Please click <a href='" . URL . "admin/Albums/manage'>here</a> to continue.");
		}

		echo "Albums: <br />\n<ul>";
		foreach($this->getAlbums(false) as $album){
			echo "\t\t\t<li>" . $album['name'] . " <a href='" . URL . "admin/Albums/edit/" . $album['id'] . "'>Edit</a> " .
					"<a href='" . URL . "admin/Albums/manage/delete/" . $album['id'] . "'>Delete</a></li>\n";
		}	
		echo "</ul>";
			
	}
	
	public function edit(){
		if(!isset($this->lncln->params[2])){
			$this->lncln->display->message("Please don't come here on your own.");
		}
		
		if(isset($_POST['name'])){
			$this->lncln->display->message($this->changeAlbumName($_POST['id'], $_POST['name']) .
				"  Click <a href='" . URL . "admin/Albums/manage'>here</a> to continue");
		}
		
		$id = $this->lncln->params[2];
		$album = $this->getAlbumName($id);
		
		$form = array(
			'action' => 'admin/Albums/edit/' . $id,
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Edit Album',
			);
		
		$form['inputs'][] = array(
			'title' => '',
			'type' => 'hidden',
			'name' => 'id',
			'value' => $id,
			);
			
		$form['inputs'][] = array(
			'title' => 'Album Name:',
			'type' => 'text',
			'name' => 'name',
			'value' => $album,
			);
		
		echo create_form($form);
	}
	
	/**
	 * Registers actions that will be used in the admin panel
	 * @since 0.13.0
	 * 
	 * @return array Keys: url 
	 */
	public function actions(){
		$action = array(
			'urls' => array(
				'add' => 'Add album',
				'manage' => 'Manage albums',
				'edit' => '',
				),
			);
		
		return $action;
	}
	
	/**
	 * Adds an album to the database
	 * @since 0.9.0
	 * 
	 * @param string $name The name of the album
	 * 
	 * @return string If it passed or not
	 */
	protected function addAlbum($name){
		$name = $this->db->prep_sql($name);
		
		$sql = "SELECT COUNT(name) as name FROM albums WHERE name = '" . $name ."'";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
		if($row['name'] > 0){
			return "Album already exists";
		}
		
		$sql = "INSERT INTO albums (name) VALUES (\"" . $name . "\")";
		$this->db->query($sql);
		
		if($this->db->affected_rows() > 0){
			return "Add album " . $name . " successfully.";
		}
		else{
			return "Album not added";
		}
	}
	
	/**
	 * Deletes an album
	 * @since 0.9.0
	 * 
	 * @param int $album The album id to be deleted
	 */
	protected function deleteAlbum($album){
		$album = $this->db->prep_sql($album);
		
		if(is_numeric($album)){
			$sql = "UPDATE images SET album = 0 WHERE album = " . $album;
			$this->db->query($sql);
			
			$sql = "DELETE FROM albums WHERE id = " . $album;
			$this->db->query($sql);
		}		
	}
	
	/**
	 * Changes the album's name
	 * @since 0.11.0
	 * 
	 * @param $id int Album id
	 * @param $name string New name
	 */
	protected function changeAlbumName($id, $name){
		$id = $this->db->prep_sql($id);
		$name = $this->db->prep_sql($name);
		
		$sql = "SELECT COUNT(name) as name FROM albums WHERE name = '" . $name ."'";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
		if($row['name'] > 0){
			return "Album already exists.";
		}
		
		$sql = "UPDATE albums SET name = '" . $name ."' WHERE id = " . $id;
		$this->db->query($sql);
		
		return "Album updated successfully.";
	}
}
