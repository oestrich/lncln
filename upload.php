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
		echo $key;
		
		print_r($_POST['images'][$key]);
		
		//$lncln->upload($key, $_POST['images'][$key]);
		
		/*
	    if($_POST['images'][$key]['tags'] == ""){
            $_SESSION['upload'][$_SESSION['uploadKey'][$key]] = 3;
            $lncln->delete($key);
            continue;
        }		
		$lncln->tag($key, $_POST['images'][$key]['tags']);
		$lncln->caption($key, $_POST['images'][$key]['caption']);
		$lncln->changeAlbum($key, $_POST['images'][$key]['album']);
		
		if($_POST['obscene'][$key]){
			$lncln->obscene($key);
		}
		
		$sql = "UPDATE images SET uploaded = 1 WHERE id = " . $key;
		mysql_query($sql);
		*/
	}
	//header("location:" . URL . "index.php");
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
	?>
		<div id="<?=$image['id'];?>" class="modDiv">
			<input type="hidden" name='check[<?=$image;?>]' value="<?=$image;?>" />
			Obscene: <input type="checkbox" name="image[<?=$image;?>][obscene]" id="check<?=$image;?>" /> 
			<a href="<?=URL;?>images/temp/<?=$image;?>" target="_blank" class="modImage"><img src="<?=URL;?>images/temp/<?=$image;?>" height="150" width="150"/></a>
			<div class="modForms">
				<input type="hidden" name="images[<?=$image;?>][id]" value="<?=$image;?>" /><br />
				Tags:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name='images[<?=$image;?>][tags]' /><br />
				Caption:&nbsp;<textarea name="images[<?=$image;?>][caption]" rows="10" cols="50" wrap="off"></textarea><br />
				Album:&nbsp;&nbsp;&nbsp;
				<select name="images[<?=$image;?>][album]" onfocus="modCheck('<?=$image;?>')">
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