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
		<table>
			<tr>
				<td>Title:</td>
				<td><input type="text" name="title" size="53"/></td>
			</tr>
			<tr>
				<td>Body:</td>
				<td><textarea name="body" cols="40" rows="10"></textarea></td>
			</tr>
		</table>
		<input type="submit" value="Submit" />
	</div>
</form>