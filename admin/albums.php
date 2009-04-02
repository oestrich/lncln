<?
/**
 * albums.php
 * 
 * Manage albums, create, delete, not edit though.  Go figure that
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0 $Id: adduser.php 187 2009-02-25 01:28:19Z eric $
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("../load.php");

$lncln = new lncln();

if(isset($_POST['name'])){
	$album = $lncln->addAlbum($_POST['name']);
}

if($_GET['action'] == "delete"){
	$lncln->deleteAlbum($_GET['album']);
}

require_once("../includes/header.php");

if($lncln->user->permissions['isAdmin'] == 1){
		
	if(isset($album)){
		echo $album . "<br />";
	}
	
	$sql = "SELECT id, name FROM albums WHERE 1";
	$result = mysql_query($sql);
	
	?>
		Albums: <br />
		<ul>
<?
	while($row = mysql_fetch_assoc($result)){
		echo "\t\t\t<li>" . $row['name'] . " <a href='albums.php?action=delete&amp;album=" . $row['id'] . "'>Delete</a></li>\n";
	}	
?>
		</ul>
		<br />
		<form action="albums.php" method="post">
			<div>
				Add new album:<br />
				<input type="text" name="name" />
				<input type="submit" value="Add album"/>
			</div>
		</form>
<?
}
else{
	header("location:". URL . "index.php");
}

require_once("../includes/footer.php");
?>