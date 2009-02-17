<?
/**
 * adduser.php
 * 
 * @author Eric Oestrich
 * @version 0.6.0
 * 
 * @package lncln
 */

require_once("../includes/config.php");
require_once("../includes/functions.php");

connect();

$lncln = new lncln();
$lncln->loggedIn();


if(isset($_POST['username'])){
	$added = $lncln->adduser($_POST);
}

require_once("../includes/header.php");
if($lncln->isAdmin){
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
else{
	echo "What are you doing here?";
}


require_once("../includes/footer.php");
?>