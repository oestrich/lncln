<?
/**
 * add.php
 * 
 * Let's an admin create a user, what else?
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
?>

<form action="<?=createLink("add");?>" method="post" />
	<div>
		<table>
			<tr>
				<td>Name:</td>
				<td><input type="text" name="name" /></td>
			</tr>
		</table>
	</div>
</form>