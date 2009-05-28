<?php
/**
 * class.user.php
 * 
 * User class
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
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
	public $db = null;
	
	public $username;  //String, username
	public $userID;  //Int, the user's id
	public $group;
	
	public $isUser = false; //registered user or just anonymous
	
	public $permissions = array(); //Array(bool), contains user permissions
	
	/**
	 * Sets up the permissions array, checks if user is logged in, etc
	 * Starts with default values, and then fills in where appropriate
	 * @since 0.10.0
	 */
	function __construct(){
		global $db;
		$this->db = $db;
		
		$this->loggedIn();
		
		$this->permissions = array(
				"isAdmin" => 0,
				"toQueue" => 1
				);
		
		$sql = "SELECT * FROM users WHERE id = " . $this->userID . " LIMIT 1";
		$this->db->query($sql);
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
		$sql = "SELECT * FROM groups WHERE id = " . $this->group . " LIMIT 1";
		$this->db->query($sql);
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
    
        $sql = "SELECT id, name FROM users WHERE name = '" . $username . "' AND password = '" . $password . "'";
        $this->db->query($sql);

        $row = $this->db->fetch_one();
        
        $this->userID = $row['id'];
        $this->username = $row['name'];

        //removes any cookies that may have been set.
        if(!isset($_COOKIE['password']) && $_COOKIE['username']){
            setcookie("username", "", time() - (60 * 60 * 24));
            setcookie("password", "", time() - (60 * 60 * 24));
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
			$sql = "UPDATE users " .
					"SET postTime = " . time() . ", numImages = numImages + 1, uploadCount = uploadCount + 1 " .
					"WHERE id = '" . $this->userID . "' " .
					"LIMIT 1"; 
			$this->db->query($sql);
	 	}
	 	
	 	$sql = "SELECT postTime, numImages FROM users WHERE id = " . $this->userID;
	 	$result = mysql_query($sql);
	 	$row = mysql_fetch_assoc($result);
	 	
	 	//Number images <= group limit goto homepage, if 0 unlimited
	 	if($row['numImages'] <= $this->permissions['numIndex'] || ($this->permissions['index'] == 1 && $this->permissions['numIndex'] == 0)){
	 		$this->permissions['toQueue'] = 0;
	 	}
	 	else{
	 		$this->permissions['toQueue'] = 1;
	 	}
	 	
	 	//If over 24 hrs later, reset number images
	 	if(date('d', $row['postTime']) != date('d', time())){
	 		$sql = "UPDATE users SET postTime = " . time() . ", numImages = " . $new . " WHERE id = '" . $this->userID . "' LIMIT 1"; 
			$this->db->query($sql);
			
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
		
		if($user['password'] != "" && $user['newPassword'] != "" && $user['newPasswordConfirm'] != ""){
			$oldPassword = $this->db->prep_sql($user['password']);
			$newPassword = $this->db->prep_sql($user['newPassword']);
			$newPasswordConfirm = $this->db->prep_sql($user['newPasswordConfirm']);
			
			$sql = "SELECT password FROM users WHERE name = '" . $username . "' LIMIT 1";
			$this->db->query($sql);
			
			$row = $this->db->fetch_one();
			
			$oldPassword = sha1($oldPassword);
			$newPassword = sha1($newPassword);
			$newPasswordConfirm = sha1($newPasswordConfirm);
			
			if($newPassword != $newPasswordConfirm || $oldPassword != $row['password']){
				return "Passwords do not match";
			}
			
			$password = "password = '" . $newPassword . "',";
			
			setcookie("password", $newPassword, time() + (60 * 60 * 24));
		}
		
		$sql = "UPDATE users SET " . $password . " obscene = " . $obscene . " WHERE name = '" . $username . "' LIMIT 1";
		$this->db->query($sql);
		
		setcookie('obscene', $obscene, time() + (60 * 60 * 24), URL);
	
		
		return "User " . $username . " updated.";
	}
}
