<?php
/**
 * user.admin.php
 * 
 * User module admin file
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Admin panel for User module
 * @since 0.13.0
 * 
 * @package lncln
 */
class UserAdmin extends User{
	
	/**
	 * Actions that User module requires
	 * @since 0.13.0
	 * 
	 * @return array Actions array
	 */
	public function actions(){
		$action = array(
			'urls' => array(
				'Main' => array(
					'add' => 'Add user',
					'manage' => 'Manage users',
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
	 * Page to add a user
	 * @since 0.13.0
	 * 
	 * @uses add_user() Create the user
	 */
	public function add(){
		if($_POST['username'] != ""){
			$this->lncln->display->message($this->add_user($_POST));
		}
		
		$form = array(
			'action' => 'admin/User/add/',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Create user',
			);
		
		$form['inputs'][] = array(
			'title' => 'Username',
			'type' => 'text',
			'name' => 'username',
			);
		
		$form['inputs'][] = array(
			'title' => 'Password',
			'type' => 'password',
			'name' => 'password',
			);
		
		$form['inputs'][] = array(
			'title' => 'Password',
			'type' => 'password',
			'name' => 'password_confirm',
			);
		
		$form['inputs'][] = array(
			'title' => 'Admin',
			'type' => 'select',
			'name' => 'admin',
			'options' => array(
				array(
					'value' => 0,
					'name' => 'No',
					),
				array(
					'value' => 1,
					'name' => 'Yes',
					),
				),
			);
		
		foreach($this->get_groups() as $group){
			$groups[] = array(
				'value' => $group['id'],
				'name' => $group['name'],
				);
		}

		$form['inputs'][] = array(
			'title' => 'Group',
			'type' => 'select',
			'name' => 'group',
			'options' => $groups,
			);
		
		create_form($form);
	}
	
	/**
	 * Manage users, links to edit and delete
	 * @since 0.13.0
	 * 
	 * @uses get_users() Pulls user list
	 */
	public function manage(){
		echo "\t\t<ul>\n";
		
		foreach($this->get_users() as $user){
			if($user['name'] == "Anonymous")
				continue;
		
			echo "\t\t\t<li>" . $user['name'] . " <a href='" . URL . "User/manage/edit/" . $user['id'] . "'>Edit</a> ";
			echo "<a href='" . URL . "User/manage/delete/" . $user['id'] . "'>Delete</a></li>\n";
		}
		
		echo "\t\t</ul>\n";
	}
	
	/**
	 * Adds a user to the system
	 * @since 0.13.0
	 * 
	 * @param array $user Contains user information
	 * 
	 * @return string Message of completion
	 */
	protected function add_user($user){
		$username = $this->db->prep_sql($user['username']);
		$password = $this->db->prep_sql($user['password']);
		$password_confirm = $this->db->prep_sql($user['password_confirm']);
		$admin = $this->db->prep_sql($user['admin']);
		$group = $this->db->prep_sql($user['group']);
		
		if(!is_numeric($user['group'])){
			return "Bad group id";
		}
		
		$password = sha1($password);
		$password_confirm = sha1($password_confirm);
		
		if($password != $password_confirm){
			return "Passwords do not match";
		}
		
		$sql = "SELECT id, name FROM users WHERE name = '" . $username . "'";
		$this->db->query($sql);
		if($this->db->num_rows() > 0){
			return "User already exists";
		}
		
		
		$sql = "INSERT INTO users (`name`, `password`, `admin`, `group`) VALUES ('" . $username . "', '" . $password . "', " . $admin . ", " . $group . ")";
		$this->db->query($sql);
		
		return "User " . $username . " added";
	}
	
	/**
	 * Return a list of users
	 * @since 0.13.0
	 * 
	 * @param int $first ID to start at
	 * @param int $limit How man IDs to pull, default 30
	 * 
	 * @return array Keys: name, id
	 */
	protected function get_users($first = 0, $limit = 30){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('id', 'name'),
			'table' => 'users',
			'limit' => array(
				$first, $limit
				),
			);
		
		$this->db->query($query);
		
		return $this->db->fetch_all();
	}
	
	/**
	 * Pull groups from groups table
	 * @since 0.13.0
	 * 
	 * @return array List of groups
	 */
	protected function get_groups(){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('id', 'name'),
			'table' => 'groups',
			);
		
		$this->db->query($query);
		
		return $this->db->fetch_all();
	}
}
