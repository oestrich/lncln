<?
/**
 * add.php
 * 
 * Let's an admin create an album
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if(isset($_POST['name'])){
	$album = $lncln->addAlbum($_POST['name']);
}

if(isset($album)){
	echo $album . "<br />";
}
?>


<form action="index.php?action=add" method="post">
	<div>
		Add new album:<br />
		<input type="text" name="name" />
		<input type="submit" value="Add album"/>
	</div>
</form>