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
		
		if(!is_numeric($user['group'])){
			return "Bad group id";
		}
			
		$group = $user['group'];
		
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
}
