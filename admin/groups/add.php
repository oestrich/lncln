<?
/**
 * add.php
 * 
 * Let's an admin create a group, what else?
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
 
if(isset($_POST['name'])){
	echo $lncln->addGroup($_POST);
}
?>

<form action="<?=createLink("add");?>" method="post" />
	<div>
		<table>
			<tr>
				<td>Name:</td>
				<td><input type="text" name="name" /></td>
			</tr>
			<tr>
				<td>Directly to Index:</td>
				<td><select name="index" ><option value="0">No</option><option value="1">Yes</option></select></td>
			</tr>
			<tr>
				<td title="Number of images that go directly to the homepage">Number to Index:</td>
				<td><input type="text" name="numIndex" size="3" /></td>
			</tr>
			<tr>
				<td>Report:</td>
				<td><select name="rate" ><option value="0">No</option><option value="1">Yes</option></select></td>
			</tr>
			<tr>
				<td>Report Value:</td>
				<td><input type="text" name="reportValue" size="3" /></td>
			</tr>
			<tr>
				<td>Rate:</td>
				<td><select name="rate" ><option value="0">No</option><option value="1">Yes</option></select></td>
			</tr>
			<tr>
				<td>Rate Value:</td>
				<td><input type="text" name="rateValue" size="3" /></td>
			</tr>
			<tr>
				<td>Use obscene:</td>
				<td><select name="obscene" ><option value="0">No</option><option value="1">Yes</option></select></td>
			</tr>
			<tr>
				<td>Use refresh:</td>
				<td><select name="refresh" ><option value="0">No</option><option value="1">Yes</option></select></td>
			</tr>
			<tr>
				<td>Use delete:</td>
				<td><select name="delete" ><option value="0">No</option><option value="1">Yes</option></select></td>
			</tr>
			<tr>
				<td>Use caption:</td>
				<td><select name="caption" ><option value="0">No</option><option value="1">Yes</option></select></td>
			</tr>
			<tr>
				<td>Use tag:</td>
				<td><select name="tag" ><option value="0">No</option><option value="1">Yes</option></select></td>
			</tr>
			<tr>
				<td>Manage albums:</td>
				<td><select name="album" ><option value="0">No</option><option value="1">Yes</option></select></td>
			</tr>
		</table>
		<input type="submit" value="Submit" />
	</div>
</form>