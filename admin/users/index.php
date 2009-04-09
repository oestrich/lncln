<?
/**
 * index.php
 * 
 * Main page for the user admin module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

require_once("../../load.php");

$lncln = new lncln();

include("../admin.php");

require_once(ABSPATH . "includes/header.php");

if($_GET['action'] == 'add'){
	if(isset($_POST['username'])){
		$added = $lncln->adduser($_POST);
	}
	
	if(isset($added)){
		echo $added;
	}
	?>
	<form enctype="multipart/form-data" action="index.php?action=add" method="post">
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

require_once(ABSPATH . "includes/footer.php");
?>