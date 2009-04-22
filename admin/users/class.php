<?
/**
 * class.php
 * 
 * Main class for the user admin module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
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
		$username = prepareSQL($user['username']);
		$password = prepareSQL($user['password']);
		$passwordConfirm = prepareSQL($user['passwordconfirm']);
		$admin = prepareSQL($user['admin']);
		
		$password = sha1($password);
		$passwordConfirm = sha1($passwordConfirm);
		
		if($password != $passwordConfirm){
			return "Passwords do not match";
		}
		
		$sql = "SELECT id, name FROM users WHERE name = '" . $username . "'";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0){
			return "User already exists";
		}
		
		
		$sql = "INSERT INTO users (name, password, admin) VALUES ('" . $username . "', '" . $password . "', " . $admin . ")";
		mysql_query($sql);
		
		return "User " . $username . " added";
	}
	
	/**
	 * Changes the user's information and permissions
	 * 
	 * @since 0.11.0
	 * @package lncln
	 * 
	 * @param $info array Contains the users information
	 */
	function changeUser($info){
		if(is_numeric($info['admin']) && is_numeric($info['viewObscene']) && is_numeric($info['id'])){
			$admin = $info['admin'];
			$obscene = $info['viewObscene'] ? 1 : 0;
			$id = $info['id'];
		}
		else{
			return "";
		}
		
		if($info['password'] != "" && $info['confirm'] != ""){
			$password = prepareSQL($info['password']);
			$confirm = prepareSQL($info['confirm']);
			
			$password = sha1($password);
			$confirm = sha1($confirm);
			
			if($password == $confirm){
				$passwordSQL = ", password = '" . $password . "' ";
			}
		}
		
		$sql = "UPDATE users SET admin = " . $admin . ", obscene = " . $obscene . " " . $passwordSQL . " WHERE id = " . $id;
		mysql_query($sql);
	}
	
	/**
	 * Deletes the user associated to the id
	 * No going back after you call this
	 * 
	 * @since 0.11.0
	 * @package lncln
	 * 
	 * @param $id int User id to be deleted
	 */
	function deleteUser($id){
		if($id == $this->user->userID)
			return "";
			
		if(is_numeric($id)){
			$sql = "DELETE FROM users WHERE id = " . $id;
			mysql_query($sql);
			
			$sql = "DELETE FROM rating WHERE userID = " . $id;
			mysql_query($sql);
		}
	}
		
	/**
	 * Return all users currently in the system
	 * 
	 * @since 0.11.0
	 * @package lncln
	 * 
	 * @return array Contains all users. Keys: id, name
	 */
	function getUsers(){
		$sql = "SELECT id, name FROM users WHERE 1";
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_assoc($result)){
			$users[] = array("id"	 => $row['id'],
							  "name" => $row['name']
							  );	
		}
		
		return $users;
	}
	
	/**
	 * Returns all information regarding on user
	 * 
	 * @since 0.11.0
	 * @package lncln
	 * 
	 * @return array Information on user. Keys: id, name
	 */
	function getUser($id){
		$id = prepareSQL($id);
		
		$sql = "SELECT * FROM users WHERE id = " . $id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);	
		
		return $row;
	}
	
	/**
	 * Lists groups in a select style.
	 * 
	 * @since 0.12.0
	 * @package lncln
	 * 
	 * @return string Contains a select for all groups
	 */
	function listGroups(){
		$sql = "SELECT id, name FROM groups WHERE 1";
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_assoc($result)){
			$groups[] = array(  "id" => $row['id'],
								"name" => $row['name']
								);
		}
		
		$select = "<select name='group'>";
		
		foreach($groups as $group){
			$select .= "<option value='" . $group['id'] . "'>" . $group['name'] . "</option>";
		}
		
		$select .= "</select>";
		
		return $select;
	}
}