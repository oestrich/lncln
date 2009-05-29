<?php
/**
 * class.php
 * 
 * Main User class
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
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
class User{
	/**
	 * @var string Name of module
	 */
	public $name = "User";
	
	/**
	 * @var string Display name of module
	 */
	public $displayName = "User";
	
	/**
	 * @var Database Reference to the Database instance
	 */
	public $db = null;
	
	/**
	 * Construct to pass the reference of lncln so that modules 
	 * can access permissions and settings
	 * @since 0.13.0
	 * 
	 * @param lncln &$lncln Main class variable
	 */
	public function __construct(&$lncln){
		$this->db = get_db();
		
		$this->lncln = $lncln;
	}
	
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
			'type' => 'text',
			'name' => 'password',
			);
		
		$form['inputs'][] = array(
			'title' => 'New Password',
			'type' => 'text',
			'name' => 'newPassword',
			);
			
		$form['inputs'][] = array(
			'title' => 'New Password',
			'type' => 'text',
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
		
		$sql = "SELECT obscene FROM users WHERE name = '" . $username . "' AND password = '" . $password . "'";
		$this->db->query($sql);
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
}
