<?php
/**
 * class.php
 * 
 * Main User class
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Main class for the User module
 * @since 0.13.0
 * 
 * @package lncln
 */
class User extends Module{
	/**
	 * @var string Name of module
	 */
	public $name = "User";
	
	/**
	 * @var string Display name of module
	 */
	public $displayName = "User";
	
	/**
	 * Manages the different pages for User module
	 * @since 0.13.0
	 */
	public function index(){
		if($this->lncln->params[0] == "login"){
			$this->login();
		}
		elseif($this->lncln->params[0] == "logout"){
			$this->logout();
		}
		elseif($this->lncln->params[0] == "register"){
			$this->register();
		}
		else{
			if($this->lncln->user->isUser){
				$this->settings();
			}
			else{
				$this->lncln->display->message("Please sign in first.");
			}
		}
	}
	
	/**
	 * Log in/out links in the header
	 * @since 0.13.0
	 * 
	 * @return string Links
	 */
	public function header_link(){
		if($this->lncln->user->isUser == false){
			$output = "<a href='" . URL . "user/login'>Log in</a>";
			
			if($this->lncln->display->settings['register'] == true){
				$output .= " <a href='" . URL . "user/register'>Register</a>";
			}
		}
		else{
			$output = "<a href='" . URL . "user/logout'>Log out " . $this->lncln->user->username . "</a> " .
					"<a href='" . URL . "user/'>Change Settings</a>";
		}
		
		return $output;
	}
	
	/**
	 * Lets users change their settings
	 * @since 0.13.0
	 */
	public function settings(){
		if(isset($_POST['username'])){
			$this->lncln->display->message($this->lncln->user->updateUser($_POST) . "<br />Click <a href='" . URL . "index.php'>here</a> to continue");
		}
		
		$form = array(
			'action' => 'user/',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Change settings',
			);
		
		$form['inputs'][] = array(
			'type' => 'hidden',
			'name' => 'username',
			'value' => $this->lncln->user->username,
			);
		
		$form['inputs'][] = array(
			'title' => 'Only put in your password if you want to change it',
			'type' => 'description',
			);
		
		$form['inputs'][] = array(
			'title' => 'Old Password',
			'type' => 'password',
			'name' => 'password',
			);
		
		$form['inputs'][] = array(
			'title' => 'New Password',
			'type' => 'password',
			'name' => 'newPassword',
			);
			
		$form['inputs'][] = array(
			'title' => 'New Password',
			'type' => 'password',
			'name' => 'newPasswordConfirm',
			);
		
		$form['inputs'][] = array(
			'title' => 'View Obscene',
			'type' => 'select',
			'name' => 'obscene',
			'options' => array(
					array(
						'name' => 'No',
						'value' => 0,
						'selected' => $_COOKIE['obscene'] == 0 ? true : false,
						),
					array(
						'name' => 'Yes',
						'value' => 1,
						'selected' => $_COOKIE['obscene'] == 1 ? true : false,
						),
				),
			);
			
		create_form($form);
	}
	
	/**
	 * Log in page
	 * @since 0.13.0
	 * 
	 * @uses _login() Handles the actual logging in
	 */
	protected function login(){
		if($this->lncln->user->isUser == true){
			header("location:" . URL . "index/");
		}
		if(isset($_POST['username']) || (!isset($_COOKIE['password']) && isset($_COOKIE['username']))){
			if($this->_login()){
				$this->lncln->display->message("Welcome " . $this->lncln->user->username . ". " .
						"Click <a href='" . URL . "index/'>here</a> to continue.");
			}
		}
		
		$form = array(
			'action' => 'user/login',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Login',
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
		
		create_form($form);
	}
	
	/**
	 * Actually does the logging in
	 * @since 0.13.0
	 * 
	 * @todo Let modules decide what is pulled for fields logging in, obscene should not be hard coded in
	 * @todo Let modules decide what cookies need to be stored outside of user/pass
	 * 
	 * @return bool True if logged in
	 */
	protected function _login(){
		if(!isset($_COOKIE['password']) && !isset($_POST['username'])){
			setcookie("username", "", time() - 30, URL);
			header("location:" . URL . "user/login/");
			exit();
		}
		
		$username = $this->db->prep_sql($_POST['username']);
		$password = $this->db->prep_sql($_POST['password']);
	
		if(isset($_POST['username'])){
			$password = sha1($password);
		}
		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('obscene'),
			'table' => 'users',
			'where' => array(
				'AND' => array(
					array(
						'field' => 'name',
						'compare' => '=',
						'value' => $username,
						),
					array(
						'field' => 'password',
						'compare' => '=',
						'value' => $password,
						),
					),
				),
			);
		
		$this->db->query($query);
		$numRows = $this->db->num_rows();
		
		$row = $this->db->fetch_one();
		
		if($numRows == 1){
			$obscene = $row['obscene'] == 1 ? true : false;
	
			setcookie("username", $username, time() + (60 * 60 * 24), URL);
			setcookie("password", $password, time() + (60 * 60 * 24), URL);
			setcookie("obscene", $obscene, time() + (60 * 60 * 24), URL);
			
			$this->lncln->user->username = $username;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Log out page
	 * @since 0.13.0
	 */
	protected function logout(){
		setcookie("username", "", time() - (60 * 60 * 24), URL);
		setcookie("password", "", time() - (60 * 60 * 24), URL);
		
		header("location:" . URL . "index/");
		exit();
	}
	
	/**
	 * Registration page
	 * @since 0.13.0
	 * 
	 * @uses _register() Function that handles 
	 */
	protected function register(){
		if($this->lncln->display->settings['register'] == true){
			if($_POST['username'] != ""){
				$this->_register($_POST);
			}
			
			$form = array(
				'action' => 'user/register/',
				'method' => 'post',
				'inputs' => array(),
				'file' => false,
				'submit' => 'Login',
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
			
			echo "Please fill out all fields.";
			create_form($form);
		}
		else{
			$this->lncln->display->message("This site does not currently allow registerations.");
		}
	}
	
	/**
	 * Sets up settings required for a regular user to be created
	 * @since 0.13.0
	 * 
	 * @param array $user User data passed from $_POST
	 * 
	 * @uses UserAdmin::add_user() Actually handles registration
	 */
	protected function _register($user){
		/** Include the admin file if it hasn't alread */
		include_once("user.admin.php");
		
		$user['admin'] = 0;
		$user['group'] = $this->lncln->display->settings['default_group'];
		
		$message = UserAdmin::add_user($user);
		
		$this->lncln->display->message("You have been successfully registered " 
			. $user['username'] . ". Please click <a href='" . URL . "user/login/'>" .
					"here</a> to login.");
	}
}
