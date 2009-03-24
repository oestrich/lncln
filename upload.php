<?php
/**
 * upload.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0  $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
 
require_once("load.php");

//Makes sure users don't come to this page without being sent here.  Otherwise things might get messed up.
if(!isset($_POST['type'])){
	require_once("includes/header.php");
	echo "Please don't come to this page on your own.";
	require_once("includes/footer.php");
	exit();
}

$lncln = new lncln("upload");

require_once(ABSPATH . "includes/header.php");

?>
	<form action="upload.php?action=finishUpload<?=$img;?>" method="post">
<?
foreach($lncln->images as $image):
	?>
		<div id="<?=$image['id'];?>" class="modDiv">
			Obscene: <input type="checkbox" name="check[<?=$image['id'];?>]" id="check<?=$image['id'];?>" /> 
			<a href="<?=URL;?>images/full/<?=$image['file'];?>" target="_blank" class="modImage"><img src="<?=URL;?>images/thumb/<?=$image['file'];?>" /></a>
			<div class="modForms">
				<input type="hidden" name="images[<?=$image['id'];?>][id]" value="<?=$image['id'];?>" /><br />
				Tags:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name='images[<?=$image['id'];?>][tags]' /><br />
				Caption:&nbsp;<textarea name="images[<?=$image['id'];?>][caption]" rows="10" cols="50" wrap="off"></textarea><br />
				Album:&nbsp;&nbsp;&nbsp;
				<select name="images[<?=$image['id'];?>][album]" onfocus="modCheck('<?=$image['id'];?>')">
					<option value="0">No album</option>
		<?foreach($lncln->getAlbums() as $album):?>
					<option value="<?=$album['id'];?>" ><?=$album['name'];?></option>
		<?endforeach;?>
				</select>
			</div>
		</div>

<?
endforeach;
?>
		<input type="submit" value="Submit" />
	</form>
<?

require_once(ABSPATH . "includes/footer.php");

?>