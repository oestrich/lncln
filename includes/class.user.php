<?php
/**
 * class.user.php
 * 
 * User class
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Main User class, not the module
 * Contains all the information regarding a user
 * @since 0.10.0
 * 
 * @package lncln
 */
class MainUser{
	/**
	 * @var Database Reference to the Database instance
	 */
	public $db = null;
	
	/**
	 * @var string Username of currently logged in user
	 */
	public $username;
	
	/**
	 * @var int ID of currently logged in user
	 */
	public $userID;
	
	/**
	 * @var int Group ID of currently logged in user
	 */
	public $group;
	
	/**
	 * @var bool If a user is logged in
	 */
	public $isUser = false; //registered user or just anonymous
	
	/**
	 * @var array Permissions for currently logged in user
	 */
	public $permissions = array(); //Array(bool), contains user permissions
	
	/**
	 * Sets up the permissions array, checks if user is logged in, etc
	 * Starts with default values, and then fills in where appropriate
	 * @since 0.10.0
	 */
	function __construct(){
		$this->db = get_db();
		
		$this->loggedIn();
		
		$this->permissions = array(
				"isAdmin" => 0,
				"toQueue" => 1
				);
		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('*'),
			'table' => 'users',
			'where' => array(
				array(
					'field' => 'id',
					'compare' => '=',
					'value' => $this->userID
					),
				),
			'limit' => array(1),
			);
		
		$this->db->query($query);
		
		$row = $this->db->fetch_one();
		
		$this->permissions['isAdmin'] = $row['admin'];
		$this->group = $row['group'];
		
		$this->loadPermissions();
		
		$this->checkUploadLimit();
	}
	
	/**
	 * Loads user permissions
	 * @since 0.12.0
	 */
	function loadPermissions(){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('*'),
			'table' => 'groups',
			'where' => array(
				array(
					'field' => 'id',
					'compare' => '=',
					'value' => $this->group,
					),
				),
			'limit' => array(1),
			);
		
		$this->db->query($query);
		
		$row = $this->db->fetch_one();
		
		foreach($row as $key => $permission){
			if($key == "id" || $key == "name")
				continue;
			
			$this->permissions[$key] = $permission;
		}
	}
	
	/**
	 * Checks to see if a user is logged in
	 * Moved from lncln as of 0.10.0
	 * @since 0.10.0
	 * 
	 * @todo Move to a session based system as well, not just relying on cookies
     */
    function loggedIn(){
        if(isset($_COOKIE['password']) && isset($_COOKIE['username'])){
            $username = $this->db->prep_sql($_COOKIE['username']);
            $password = $this->db->prep_sql($_COOKIE['password']);
            
            $this->isUser = true;
        }
        else{
            $username = "Anonymous";
            $password = "";
            
            $this->isUser = false;
        }
    
        $query = array(
			'type' => 'SELECT',
			'fields' => array('id', 'name'),
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

        $row = $this->db->fetch_one();
        
        $this->userID = $row['id'];
        $this->username = $row['name'];

        //removes any cookies that may have been set.
        if(!isset($_COOKIE['password']) && $_COOKIE['username']){
            setcookie("username", "", time() - (60 * 60 * 24), URL);
            setcookie("password", "", time() - (60 * 60 * 24), URL);
            header("location:". URL . "index.php");
        }
    }
	
	/**
	 * Checks if a user can upload straight to the homepage
	 * @since 0.10.0
	 * 
	 * @param bool $new If a user uploaded a new image, defaults to 0
	 */
	 function checkUploadLimit($new = 0){
	 	if($new == 1){
			$query = array(
				'type' => 'UPDATE',
				'table' => 'users',
				'set' => array(
					'postTime' => time(),
					'numImages' => '!`numImages` + 1',
					'uploadCount' => '!`uploadCount` + 1',
					),
				'where' => array(
					array(
						'field' => 'id',
						'compare' => '=',
						'value' => $this->userID
						),
					),
				'limit' => array(1),
				);
			
			$this->db->query($query);
	 	}
	 	
	 	$query = array(
	 		'type' => 'SELECT',
	 		'fields' => array('postTime', 'numImages'),
	 		'table' => 'users',
	 		'where' => array(
	 			array(
	 				'field' => 'id',
	 				'compare' => '=',
	 				'value' => $this->userID,
	 				),
	 			),
	 		);
	 	
		$this->db->query($query);
	 	$row = $this->db->fetch_one();
	 	
	 	//Number images <= group limit goto homepage, if 0 unlimited
	 	if($row['numImages'] <= $this->permissions['numIndex'] || 
	 		($this->permissions['index'] == 1 && $this->permissions['numIndex'] == 0)){
	 		
	 		$this->permissions['toQueue'] = 0;
	 	}
	 	else{
	 		$this->permissions['toQueue'] = 1;
	 	}
	 	
	 	//If over 24 hrs later, reset number images
	 	if(date('d', $row['postTime']) != date('d', time())){
	 		$query = array(
	 			'type' => 'UPDATE',
	 			'table' => 'users',
	 			'set' => array(
	 				'postTime' => time(),
	 				'numImages' => 1,
	 				),
	 			'where' => array(
	 				array(
	 					'field' => 'id',
	 					'compare' => '=',
	 					'value' => $this->userID,
	 					),
	 				),
	 			'limit' => array(1),
	 			);
	 		
			$this->db->query($query);
			
			$this->permissions['toQueue'] = 0;
	 	}
	 	
	 	if($this->permissions['index'] == 0){
	 		$this->permissions['toQueue'] = 1;
	 	}
	 }
	 
	/**
	 * Updates a user's information.
	 * Moved from lncln main class
	 * @since 0.5.0
	 * 
	 * @param array $user Contains the user's updated information
	 * 
	 * @return string Whether it updated or not
	 */
	function updateUser($user){
		$username = $this->db->prep_sql($user['username']);
		$obscene = $this->db->prep_sql($user['obscene']);
		$password = array();		
		
		if($user['password'] != "" && $user['newPassword'] != "" && 
			$user['newPasswordConfirm'] != ""){
			
			$oldPassword = $this->db->prep_sql($user['password']);
			$newPassword = $this->db->prep_sql($user['newPassword']);
			$newPasswordConfirm = $this->db->prep_sql($user['newPasswordConfirm']);
			
			$query = array(
				'type' => 'SELECT',
				'fields' => array('password'),
				'table' => 'users',
				'where' => array(
					array(
						'field' => 'name',
						'compare' => '=',
						'value' => $username,
						),
					),
				'limit' => array(1),
				);
			
			$this->db->query($query);
			
			$row = $this->db->fetch_one();
			
			$oldPassword = sha1($oldPassword);
			$newPassword = sha1($newPassword);
			$newPasswordConfirm = sha1($newPasswordConfirm);
			
			if($newPassword != $newPasswordConfirm || $oldPassword != $row['password']){
				return "Passwords do not match";
			}
			
			$password = array(
				'password' => $newPassword,
				);
			
			setcookie("password", $newPassword, time() + (60 * 60 * 24), URL);
		}
		
		$query = array(
			'type' => 'UPDATE',
			'table' => 'users',
			'set' => array(
				'obscene' => $obscene,
				),
			'where' => array(
				array(
					'field' => 'name',
					'compare' => '=',
					'value' => $username,
					),
				),
			'limit' => array(1),
			);
		$query['set'] = array_merge($query['set'], $password);
			
		$this->db->query($query);
		
		setcookie('obscene', $obscene, time() + (60 * 60 * 24), URL);
	
		
		return "User " . $username . " updated.";
	}
}
