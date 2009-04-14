<?
/**
 * add.php
 * 
 * Let's an admin create a news
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
 
?>

<form action="<?=createLink("add", array());?>" method="post" />
	<div>
		Title: <input type="text" name="title" /><br />
		Body: <textarea name="body"></textarea><br />
		<input type="submit" value="Submit" />
	</div>
</form>