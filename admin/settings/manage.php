<?
/**
 * manage.php
 * 
 * Displays the settings to be changed
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 
?>

Change the board settings: <br />

<form action="<?=createLink("manage", array("subAction" => "edit"));?>" method="post" />
	<div>
		Board Name: <input type="text" name="name" /><br />
		<input type="button" value="Submit" />
	</div>
</form>