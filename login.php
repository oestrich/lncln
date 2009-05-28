<?
/**
 * login.php
 * 
 * Does exactly what it seems like it should
 *  
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if(isset($_POST['username']) || (!isset($_COOKIE['password']) && isset($_COOKIE['username']))){
	if(!isset($_COOKIE['password']) && !isset($_POST['username'])){
		setcookie("username", "", time() - 30, URL);
		header("location:login.php");
	}
	
	if(isset($_POST['username'])){
		$username = addslashes($_POST['username']);
		$password = addslashes($_POST['password']);
	}
	if(isset($_COOKIE['password'])){
		$username = addslashes($_POST['username']);
		$password = addslashes($_POST['password']);
	}
	
	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string($password);

	if(isset($_POST['username'])){
		$password = sha1($password);
	}
	
	$sql = "SELECT obscene FROM users WHERE name = '" . $username . "' AND password = '" . $password . "'";
	$result = mysql_query($sql);
	$numRows = mysql_num_rows($result);
	
	$row = mysql_fetch_assoc($result);
	
	if($numRows == 1){
		$obscene = $row['obscene'] == 1 ? true : false;

		setcookie("username", $username, time() + (60 * 60 * 24), URL);
		setcookie("password", $password, time() + (60 * 60 * 24), URL);
		setcookie("obscene", $obscene, time() + (60 * 60 * 24), URL);
		$isLoggedIn = true;
	}
}


			if(!$numRows && !$_COOKIE['username']){
?>
			<form enctype="multipart/form-data" action="<?=URL;?>login" method="post">
				<div>
					Username: <input type='text' name='username' id='username'/><br />
					Password: <input type='password' name='password' />
					<input type='submit' value="Login" />
				</div>
			</form>
<?
			}
			else{
?>
			<div id='loggedIn'>
				Welcome <?if(isset($_COOKIE['username'])){echo $_COOKIE['username'];}else{echo $_POST['username'];}?>!<br />
				Go back to the <a href="<?=URL;?>index/">main page</a>
			</div>
<?
			}
