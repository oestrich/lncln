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
		if($this->lncln->user->isUser){
			$this->settings();
		}
		else{
			$this->lncln->display->message("Please sign in first.");
		}
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
}