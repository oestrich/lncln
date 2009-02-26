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

$sql = "SELECT id, name FROM albums WHERE 1";
$result = mysql_query($sql);

require_once("../includes/header.php");
if($lncln->isAdmin){
	while($row = mysql_fetch_assoc($result)){
		echo $row['name'];
	}
}

require_once("../includes/footer.php");
?>