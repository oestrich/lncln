<?php
/**
 * upload.php
 * 
 * Landing page for uploading images
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */
 
require_once("load.php");

if($_GET['action'] == "finishUpload"){
	foreach($_POST['check'] as $key => $value){
		$lncln->upload($key, $_POST['images'][$key]);
	}
	header("location:" . URL . "index.php");
	exit();
}

if($_GET['action'] == "cancel"){
	foreach($_POST['images'] as $image){
		@unlink(CURRENT_IMG_TEMP_DIRECTORY . $image);
	}
	
	unset($_SESSION['uploaded']);
	unset($_SESSION['upload']);
	unset($_SESSION['uploadTime']);
	unset($_SESSION['uploadKey']);
	
	header("location:" . URL . "index.php");
	exit();
}

//Makes sure users don't come to this page without being sent here.  Otherwise things might get messed up.
if(!isset($_POST['type'])){
	$lncln->display->message("Please don't come to this page on your own.  If you didn't come here on your own, you may have uploaded more than " . ini_get("upload_max_filesize"));

}

$lncln->tempUpload();

include_once(ABSPATH . "includes/header.php");

?>
	Tags are manditory.
	<form action="upload.php?action=finishUpload<?=$img;?>" method="post">
<?
foreach($lncln->uploaded as $image):

		if(is_array($image)){
			if($image['error'] == "404")
				echo "<br />Image located at " . $image['image'] . " is 404\n<br /><br />";
			continue;
		}

		$size = getimagesize(CURRENT_IMG_TEMP_DIRECTORY . $image);
		$tHeight = ($size[1] / $size[0]) * 150;
		if($tHeight > 150){
			$thumb =  " height='150' ";
		}else{
			$thumb = " width='150' ";
		}

	?>
		<div id="<?=$image['id'];?>" class="modDiv">
			<input type="hidden" name='check[<?=$image;?>]' value="<?=$image;?>" />
			Obscene: <input type="checkbox" name="images[<?=$image;?>][obscene]" id="check<?=$image;?>" /> 
			<a href="<?=URL;?>images/temp/<?=$image;?>" target="_blank" class="modImage"><img src="<?=URL;?>images/temp/<?=$image;?>" <?=$thumb;?>/></a>
			<div class="modForms">
				<input type="hidden" name="images[<?=$image;?>][id]" value="<?=$image;?>" /><br />
				<table>
					<?foreach($lncln->modules as $module):
						if($module->upload() == "")
							continue;
					?>
					<tr>
						<td><?=$module->displayName;?>:</td>
						<td><?=createInput($module->upload(), $image);?></td>
					</tr>
					<?endforeach;?>
				</table>
			</div>
		</div>
<?
endforeach;
?>
		<input type="submit" value="Submit" />
	</form>
	<form action="upload.php?action=cancel" method="post" />
		<div>
			<?foreach($lncln->uploaded as $image){
				echo "<input type='hidden' name='images[]' value='" . $image . "' />";
			}?>
			<input type="submit" value="Cancel" />
		</div>
	</form>
<?

include_once(ABSPATH . "includes/footer.php");

?>