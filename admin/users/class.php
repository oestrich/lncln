<?
/**
 * index.php
 * 
 * Main class for the user admin module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

 
class Users extends lncln{
	/**
	 * Adds a user to the site.
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @param array $user Contains the users information, username, password, if they're an admin
	 * 
	 * @return string If bad password, or if they were added successfully
	 */
	function adduser($user){
		$username = stripslashes($user['username']);
		$password = stripslashes($user['password']);
		$passwordConfirm = stripslashes($user['passwordconfirm']);
		$admin = stripslashes($user['admin']);
	
		$username = mysql_real_escape_string($username);
		$password = mysql_real_escape_string($password);
		$passwordConfirm = mysql_real_escape_string($passwordConfirm);
		$admin = mysql_real_escape_string($admin);
		
		$password = sha1($password);
		$passwordConfirm = sha1($passwordConfirm);
		
		if($password != $passwordConfirm){
			return "Passwords do not match";
		}
		
		$sql = "SELECT id, name FROM users WHERE name = '" . $username . "'";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0){
			return "User with same name exists";
		}
		
		
		$sql = "INSERT INTO users (name, password, admin) VALUES ('" . $username . "', '" . $password . "', " . $admin . ")";
		mysql_query($sql);
		
		return "User " . $username . " added";
	}
}