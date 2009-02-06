<?
/*
/	lncln by Eric Oestrich
/	Version .5
/
*/


require_once("config.php");
require_once("functions.php");

connect($config['mysql']);
list($isLoggedIn, $isAdmin) = loggedIn();

if(isset($_POST['username'])){
	$added = adduser($_POST);
}

require_once("header.php");
if($isAdmin){
	if(isset($added)){
		echo $added;
	}
?>
	<form enctype="multipart/form-data" action="adduser.php" method="post">
			<div id="adduser">
			Username: <input type="text" name="username" /><br />
			Password: <input type='password' name='password' /><br />
			Password: <input type='password' name='passwordconfirm' /><br />
			Admin: <select name="admin">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>
			<input type="submit" value="Add user" />
		</div>
	</form>
<?
}

require_once("footer.php");
?>