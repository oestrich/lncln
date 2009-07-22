<?php
/**
 * admin.php
 * 
 * Admin file for index module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
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
	 * 
	 * @uses get_themes() Pulls themes for the select box
	 */
	public function manage(){
		$form = array(
			'action' => 'admin/Settings/manage',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Change Settings',
			);
		
		$form['inputs'][] = array(
			'title' => 'Title',
			'type' => 'text',
			'name' => 'title',
			'value' => $this->lncln->display->settings['title'],
			);
		
		$form['inputs'][] = array(
			'title' => 'Images/Page',
			'type' => 'text',
			'name' => 'perpage',
			'value' => $this->lncln->display->settings['perpage'],
			);
		
		$form['inputs'][] = array(
			'title' => 'Time between posts',
			'type' => 'text',
			'name' => 'tbp',
			'value' => $this->lncln->display->settings['tbp'],
			);
		
		$form['inputs'][] = array(
			'title' => 'Theme',
			'type' => 'select',
			'options' => $this->get_themes(),
			'name' => 'theme',
			);

		if($_POST['title'] != ""){
			foreach($form['inputs'] as $setting){
				$setting = $setting['name'];
				
				$query = array(
					'type' => 'UPDATE',
					'table' => 'settings',
					'set' => array(
						'value' => $_POST[$setting],
						),
					'where' => array(
						array(
							'field' => 'name',
							'compare' => '=',
							'value' => $setting,
							),
						),
					'limit' => array(1),
					);
				
				$this->db->query($query);
			}
			$this->lncln->display->message("Settings updated successfully. Click <a href='" . URL . "admin/Settings/manage'>here</a> to continue.");
		}

		create_form($form);
	}
	
	/**
	 * Returns a select array for managing themes
	 * @since 0.13.0
	 * 
	 * @return array Select array
	 */
	protected function get_themes(){
		$temp_themes = scandir(ABSPATH . "theme");
		
		foreach($temp_themes as $theme){
			if($theme == "." || $theme == ".." || $theme == "index.html")
				continue;
			
			if($theme == THEME){
				$selected = array('selected' => true);
			}
			else{
				$selected = array();
			}
			
			$themes[] = array_merge(
				array(
					'name' => $theme,
					'value' => $theme,
					), 
				$selected
				);
		}
		
		return $themes;
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
		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('id', 'postTime', 'title', 'news'),
			'table' => 'news',
			);
		
		$this->db->query($query);
		
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
			$query = array(
				'type' => 'SELECT',
				'fields' => array('id', 'postTime', 'title', 'news'),
				'table' => 'news',
				'where' => array(
					array(
						'field' => 'id',
						'compare' => '=',
						'value' => $id,
						),
					),
				);
			
			$this->db->query($query);
			
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
		
		$query = array(
			'type' => 'UPDATE',
			'table' => 'news',
			'set' => array(
				'title' => $title,
				'news' => $news,
				'postTime' => $postTime,
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
} 
