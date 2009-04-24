<?
/**
 * register.php
 * 
 * A new user can register
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

require_once("includes/header.php");

?>

<form action="register.php" method="post">
	<div id="adduser">
		<table>
			<tr>
				<td>Username:</td>
				<td><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type='password' name='password' /></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type='password' name='passwordconfirm' /></td>
			</tr>
		</table>
		<input type="submit" value="Submit" />
	</div>
</form>

<?
include(ABSPATH . "includes/footer.php");
?>