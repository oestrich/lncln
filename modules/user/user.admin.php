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
					'settings' => 'Change settings',
					),
				),
			'quick' => array(
				'add', 'manage', 'settings',
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
			$this->lncln->display->message($this->add_user($_POST) . ". " .
					"Click <a href='" . URL . "admin/User/manage'>here</a> to continue.");
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
	 * @uses delete_user() Delete a user
	 */
	public function manage(){
		if($this->lncln->params[2] == "delete"){
			$this->delete_user($this->lncln->params[3]);
		}
		
		echo "\t\t<ul>\n";
		
		foreach($this->get_users() as $user){
			if($user['name'] == "Anonymous")
				continue;
		
			echo "\t\t\t<li>" . $user['name'] . " <a href='" . URL . "admin/User/edit/" . $user['id'] . "'>Edit</a> ";
			echo "<a href='" . URL . "admin/User/manage/delete/" . $user['id'] . "'>Delete</a></li>\n";
		}
		
		echo "\t\t</ul>\n";
	}
	
	/**
	 * Edit users
	 * @since 0.13.0
	 * 
	 * @uses get_user() Pull a users information
	 * @uses edit_user() Change user information
	 */
	public function edit(){
		$id = $this->lncln->params[2];
		
		if(is_numeric($id)){
			if($_POST['username'] != ""){
				$this->edit_user($_POST);
				$this->lncln->display->message("User updated. Click " .
						"<a href='" . URL . "User/manage/'>here</a> to continue.");
			}
			
			$user = $this->get_user($id);
			
			$form = array(
				'action' => 'admin/User/edit/' . $id,
				'method' => 'post',
				'inputs' => array(),
				'file' => false,
				'submit' => 'Edit user',
				);
			
			$form['inputs'][] = array(
				'title' => 'Username',
				'type' => 'hidden',
				'name' => 'username',
				'value' => $user['name'],
				);
			
			$form['inputs'][] = array(
				'title' => 'Username: ' . $user['name'],
				'type' => 'description',
				'value' => $user['name'],
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
			
			$groups = $this->get_groups();
			
			foreach($groups as $group){
				$options[] = array(
					'name' => $group['name'],
					'value' => $group['id'],
					'selected' => $group['id'] == $user['group'] ? true : false,
					);
			}
			
			$form['inputs'][] = array(
				'title' => 'Group',
				'type' => 'select',
				'name' => 'group',
				'options' => $options,
				);
				
			$form['inputs'][] = array(
				'title' => 'Admin',
				'type' => 'select',
				'name' => 'admin',
				'options' => array(
					array(
						'name' => 'No',
						'value' => 0,
						'selected' => $user['admin'] == 0 ? true : false,
						),
					array(
						'name' => 'Yes',
						'value' => 1,
						'selected' => $user['admin'] == 1 ? true : false,
						),
					),
				);
			
			create_form($form);
		}
		else{
			header("location:" . URL . "admin/User/manage/");
			exit;
		}
	}
	
	/**
	 * Change user settings
	 * @since 0.13.0
	 */
	public function settings(){
		if($_POST['register'] != ""){
			$this->lncln->setting_set('register', $_POST['register']);
			$this->lncln->display->message("Settings changed. " .
				"Click <a href='" . URL ."admin/User/settings/'>here</a> to continue.");
		}
		
		$form = array(
			'action' => 'admin/User/settings/',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Submit',
			);
		
		$form['inputs'][] = array(
			'title' => 'Allow registration',
			'name' => 'register',
			'type' => 'select',
			'options' => array(
				array(
					'name' => 'No',
					'value' => 0,
					'selected' => $this->lncln->settings['register'] == 0 ? true : false,
					),
				array(
					'name' => 'Yes',
					'value' => 1,
					'selected' => $this->lncln->settings['register'] == 1 ? true : false,
					),
				),
			);
		
		create_form($form);
	}
	
	/**
	 * Edits a user's information
	 * @since 0.13.0
	 * 
	 * @param array $user User information
	 */
	protected function edit_user($user){
		$username = $this->db->prep_sql($user['username']);
		$password = $this->db->prep_sql($user['password']);
		$password_confirm = $this->db->prep_sql($user['password_confirm']);
		$admin = $this->db->prep_sql($user['admin']);
		$group = $this->db->prep_sql($user['group']);
		
		if(!is_numeric($user['group'])){
			return "Bad group id";
		}
		
		if($password != ""){
			$password = sha1($password);
			$password_confirm = sha1($password_confirm);
			
			if($password != $password_confirm){
				return "Passwords do not match";
			}
			
			$pass_sql = array(
				'password' => $password,
				);
		}
		else{
			$pass_sql = array();
		}

		$query = array(
			'type' => 'SELECT',
			'fields' => array('id'),
			'table' => 'users',
			'where' => array(
				array(
					'field' => 'name',
					'compare' => '=',
					'value' => $username,
					),
				),
			);
		
		$this->db->query($query);
		if($this->db->num_rows() != 1){
			return "User does not exist";
		}
		
		$user = $this->db->fetch_one();
		
		$query = array(
			'type' => 'UPDATE',
			'table' => 'users',
			'set' => array(
				'name' => $username,
				'admin' => $admin,
				'group' => $group,
				),
			'where' => array(
				array(
					'field' => 'id',
					'compare' => '=',
					'value' => $user['id'],
					),
				),
			);
		
		$query['set'] = array_merge($query['set'], $pass_sql);
		
		$this->db->query($query);
		
		return "User " . $username . " added";
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
	 * Deletes a user
	 * @since 0.13.0
	 * 
	 * @param int $id User ID
	 */
	protected function delete_user($id){
		if($id == $this->user->userID)
			return "";
			
		if(is_numeric($id)){
			$sql = "DELETE FROM users WHERE id = " . $id;
			$this->db->query($sql);
			
			$sql = "DELETE FROM ratings WHERE userID = " . $id;
			$this->db->query($sql);
		
			$this->lncln->display->message("User deleted.  " .
					"Click <a href='" . URL . "admin/User/manage/'>here</a> to continue managing.");
		}
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
	 * Get a user information
	 * @since 0.13.0
	 * 
	 * @param int $id User ID
	 * 
	 * @return array User information
	 */
	protected function get_user($id){
		if(is_numeric($id)){
			$query = array(
				'type' => 'SELECT',
				'fields' => array(
					'id',
					'name',
					'admin',
					'group'
					),
				'table' => 'users',
				'where' => array(
					array(
						'field' => 'id',
						'compare' => '=',
						'value' => $id,
						),
					),
				);
			
			$this->db->query($query);
			
			return $this->db->fetch_one();
		}
		
		return array();
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
 