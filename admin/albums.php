<?
/**
 * albums.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.8.0 $Id: adduser.php 187 2009-02-25 01:28:19Z eric $
 * @license license.txt GNU General Public License
 * 
 * @package lncln
 */

require_once("../load.php");

$lncln = new lncln();
$lncln->loggedIn();

require_once("../includes/header.php");

$sql = "SELECT id, name FROM albums WHERE 1";
$result = mysql_query($sql);

?>
	Albums: <br />
<?
if($lncln->isAdmin){
	while($row = mysql_fetch_assoc($result)){
		echo $row['name'] . "<br />";
	}
}

?>
	<form>
		<div>
			Add new album:<br />
			<input type="text" name="name" />
			<input type="submit" />
		</div>
	</form>
<?

require_once("../includes/footer.php");
?>