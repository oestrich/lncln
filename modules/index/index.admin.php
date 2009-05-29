<?php
/**
 * admin.php
 * 
 * Admin file for index module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Admin class for the Index Module
 * @since 0.13.0
 * 
 * @package lncln
 */
class IndexAdmin extends Index{
	/**
	 * Registers actions that will be used in the admin panel
	 * @since 0.13.0
	 * 
	 * @return array Keys: url 
	 */
	public function actions(){
		$actions = array(
			'urls' => array(
				'Main' => array(
					'manage' => 'Manage settings',
					),
				'News' => array(
					'news' => 'Manage news',
					'add' => 'Add news',
					'edit' => '',
					),
				),
			'quick' => array(
				'manage',
				),
			);
		
		return $actions;
	}
	
	/**
	 * Manage settings
	 * @since 0.13.0
	 */
	public function manage(){
		$form = array(
			'action' => 'admin/Index/manage',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Change Settings',
			);
		
		$form['inputs'][] = array(
			'title' => 'Title',
			'type' => 'text',
			'name' => 'title',
			);
		
		$form['inputs'][] = array(
			'title' => 'Images/Page',
			'type' => 'text',
			'name' => 'perpage',
			);
		
		$form['inputs'][] = array(
			'title' => 'Time between posts',
			'type' => 'text',
			'name' => 'tbp',
			);
		
		$form['inputs'][] = array(
			'title' => 'Theme',
			'type' => 'select',
			'options' => array(
				array(
					'name' => 'bbl',
					'value' => 'bbl',
					'selected' => true,
					),
				),
			'name' => 'theme',
			);
		
		create_form($form);
	}
	
	/**
	 * News functions
	 * @since 0.13.0
	 */
	public function news(){
		if($_GET['subAction'] == "delete"){
			$lncln->deleteNews($_GET['news']);
		}
		echo "News:<br />\n<ul>";
			foreach($this->get_news() as $news){
				echo "\t\t\t<li>" . $news['title'] . " <a href='" . URL . "admin/Settings/edit/" . $news['id'] . "'>Edit</a> " .
						"<a href='" . URL ."admin/Settings/news/delete/" . $news['id'] ."'>Delete</a></li>\n";
			}	
		echo "</ul>";
	}
	
	/**
	 * Add action, adds a news post
	 * @since 0.13.0
	 */
	public function add(){
		if(isset($_POST['body'])){
			echo $this->add_news($_POST);
		}
		
		$form = array(
			'action' => 'admin/Settings/add',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Add post',
			);
		
		$form['inputs'][] = array(
			'title' => 'Title',
			'type' => 'text',
			'name' => 'title',
			);
		
		$form['inputs'][] = array(
			'title' => 'Body',
			'type' => 'textarea',
			'name' => 'body',
			);
		
		create_form($form);
	}
	
	/**
	 * Adds a new news ticket type thing to the index
	 * @since 0.11.0
	 * 
	 * @param array $news Keys - title, body
	 */
	function add_news($news){
		$body = $this->db->prep_sql($news['body']);
		$title = $this->db->prep_sql($news['title']);
		
		$sql = "INSERT INTO news (postTime, title, news) VALUES (" . time() . ", '" . $title . "', '" . $body . "')";
		mysql_query($sql);
		
		return "News added.";
	}
	
	/**
	 * Returns all news
	 * @since 0.11.0
	 * 
	 * @return array Keys- id, postTime, title, news
	 */
	function get_news(){
		$news = array();
		
		$sql = "SELECT id, postTime, title, news FROM news WHERE 1";
		$this->db->query($sql);
		
		foreach($this->db->fetch_all() as $row){
			$news[] = $row;
		}
		
		return $news;
	}
	
	/**
	 * Delete a news item
	 * @since 0.11.0
	 * 
	 * @param int $id Item to be deleted
	 */
	function delete_news($id){
		if(is_numeric($id)){
			$sql = "DELETE FROM news WHERE id = " . $id;
			$this->db->query($sql);
		}
	}
	
	/**
	 * Fetch one news story
	 * @since 0.11.0
	 * 
	 * @param int $id Item to be found
	 * 
	 * @return array Keys- id, postTime, title, news
	 */
	function get_news_one($id){
		if(is_numeric($id)){
			$sql = "SELECT id, postTime, title, news FROM news WHERE id = " . $id;
			$this->db->query($sql);
			
			if($this->db->num_rows() == 1)
				return $this->db->fetch_one();
		}
	}
	
	/**
	 * Edit's news items
	 * @since 0.12.0
	 * 
	 * @param array $data Keys: title, news, postTime
	 */
	function change_news($data){
		$title = $this->db->prep_sql($data['title']);
		$news = $this->db->prep_sql($data['news']);
		
		if(is_numeric($data['postTime']) && is_numeric($data['id'])){
			$postTime = $data['postTime'];
			$id = $data['id'];
		}
		else{
			return "";
		}
		
		$sql = "UPDATE news SET title = '" . $title . "', news = '" . $news ."', postTime = " . $postTime . " WHERE id = " . $id;
		$this->db->query($sql);
	}
} 
