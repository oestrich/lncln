<?php
/**
 * upload.php
 * 
 * Landing page for uploading images
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0  $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
 
require_once("load.php");

if($_GET['action'] == "finishUpload"){
	$lncln = new lncln();
	foreach($_POST['check'] as $key => $value){
		$lncln->upload($key, $_POST['images'][$key]);
	}
	header("location:" . URL . "index.php");
	exit();
}

//Makes sure users don't come to this page without being sent here.  Otherwise things might get messed up.
if(!isset($_POST['type'])){
	require_once("includes/header.php");
	echo "Please don't come to this page on your own.";
	require_once("includes/footer.php");
	exit();
}



$lncln = new lncln("tempUpload");

require_once(ABSPATH . "includes/header.php");

?>
	Tags are manditory.
	<form action="upload.php?action=finishUpload<?=$img;?>" method="post">
<?
foreach($lncln->uploaded as $image):
		$size = getimagesize(CURRENT_IMG_TEMP_DIRECTORY . $image);
		$tHeight = ($size[1] / $size[0]) * 150;
		if($tHeight > 150){
			$thumb =  " height='". $size[0]. "' width='150' ";
		}else{
			$thumb = " height='150' width='" . $size[1] . "' ";
		}

	?>
		<div id="<?=$image['id'];?>" class="modDiv">
			<input type="hidden" name='check[<?=$image;?>]' value="<?=$image;?>" />
			Obscene: <input type="checkbox" name="images[<?=$image;?>][obscene]" id="check<?=$image;?>" /> 
			<a href="<?=URL;?>images/temp/<?=$image;?>" target="_blank" class="modImage"><img src="<?=URL;?>images/temp/<?=$image;?>" <?=$thumb;?>/></a>
			<div class="modForms">
				<input type="hidden" name="images[<?=$image;?>][id]" value="<?=$image;?>" /><br />
				Tags:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name='images[<?=$image;?>][tags]' /><br />
				Caption:&nbsp;<textarea name="images[<?=$image;?>][caption]" rows="10" cols="50" wrap="off"></textarea><br />
				Album:&nbsp;&nbsp;&nbsp;
				<select name="images[<?=$image;?>][album]">
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