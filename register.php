<?
/**
 * register.php
 * 
 * A new user can register
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

include_once("includes/header.php");

if($lncln->display->settings['register'] == 0){
	$lncln->display->message("This site has not allowed registrations.  Thank you for wanting to register though.");
}

if(isset($_POST['username']) && $lncln->display->settings['register'] == 1){
	$user = array(  "username" => $_POST['username'],
					"password" => $_POST['password'],
					"passwordconfirm" => $_POST['passwordconfirm'],
					"group" => $lncln->display->settings['defaultGroup'],
					"admin" => 0
					);
	include_once(ABSPATH . "admin/users/class.php");
	
	Users::addUser($user);
	
	$lncln->display->message("Thank you for registering " . $user['username'] . ".");
}
?>

<form action="register.php" method="post">
	<div id="adduser">
		<table>
			<tr>
				<td>Username:</td>
				<td><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type='password' name='password' /></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type='password' name='passwordconfirm' /></td>
			</tr>
		</table>
		<input type="submit" value="Submit" />
	</div>
</form>

<?
include(ABSPATH . "includes/footer.php");
?>