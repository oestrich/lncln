<?php
/**
 * admin.php
 * 
 * Administration class for Albums
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Albums' Admin class
 * @since 0.13.0
 * 
 * @package lncln
 */
class AlbumsAdmin extends Albums{
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
		
		create_form($form);
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
	
	/**
	 * Edit an album
	 * @since 0.13.0
	 */
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
			'title' => 'Album Name',
			'type' => 'text',
			'name' => 'name',
			'value' => $album,
			);
		
		create_form($form);
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
				'Main' => array(
					'add' => 'Add album',
					'manage' => 'Manage albums',
					'edit' => '',
					),
				),
			'quick' => array(
				'add', 'manage',
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
		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('!COUNT(name) as name'),
			'table' => 'albums',
			'where' => array(
				array(
					'field' => 'name',
					'compare' => '=',
					'value' => $name,
					),
				),
			);
		
		$this->db->query($query);
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
			$query = array(
				'type' => 'UPDATE',
				'table' => 'images',
				'set' => array(
					'album' => 0,
					),
				'where' => array(
					array(
						'field' => 'album',
						'compare' => '=',
						'value' => $album,
						),
					),
				);
			
			$this->db->query($query);
			
			$sql = "DELETE FROM albums WHERE id = " . $album;
			$this->db->query($sql);
		}		
	}
	
	/**
	 * Changes the album's name
	 * @since 0.11.0
	 * 
	 * @param int $id Album id
	 * @param string $name New name
	 * 
	 * @return string Message of completion
	 */
	protected function changeAlbumName($id, $name){
		$id = $this->db->prep_sql($id);
		$name = $this->db->prep_sql($name);
		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('!COUNT(name) as name'),
			'table' => 'albums',
			'where' => array(
				array(
					'field' => 'name',
					'compare' => '=',
					'value' => $name,
					),
				),
			);
		
		$this->db->query($query);
		$row = $this->db->fetch_one();
		
		if($row['name'] > 0){
			return "Album already exists.";
		}
		
		$query = array(
			'type' => 'UPDATE',
			'table' => 'albums',
			'set' => array(
				'name' => $name,
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
		
		return "Album updated successfully.";
	}
}
