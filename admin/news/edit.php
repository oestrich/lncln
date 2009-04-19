<?
/**
 * edit.php
 * 
 * Let's an admin edit a news item
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if(!isset($_GET['news'])){
	echo "Please don't come here on your own.";
	include(ABSPATH . "includes/footer.php");
	exit();
}

if(isset($_POST['id'])){
	$lncln->changeNews($_POST);
	header("location:" . createLink("manage"));
	exit();
}

$news = $lncln->getNewsOne($_GET['news']);
?>

<form action="<?=createLink("edit", array("news" => $news['id']));?>" method="post" />
	<div>
		<input type="hidden" name="id" value="<?=$news['id'];?>" />
		<table>
			<tr>
				<td>Title:</td>
				<td><input type="text" name="title" size="53" value="<?=$news['title'];?>"/></td>
			</tr>
			<tr style="vertical-align: top;">
				<td>Body:</td>
				<td><textarea name="news" cols="40" rows="10"><?=$news['news'];?></textarea></td>
			</tr>
			<tr>
				<td>Post Time*:</td>
				<td><input type="text" name="postTime" size="53" value="<?=$news['postTime'];?>" /></td>
			</tr>
			<tr>
				<td colspan="2">*Post time is in Unix time.</td>
			</tr>
		</table>
		<input type="submit" value="Submit" />
	</div>
</form>