<?
/**
 * moderate.php
 * 
 * Easily moderate 50 images at a time.  Bulk editing has never been easier!
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

require_once("../load.php");

$lncln = new lncln("index");

include("admin.php");

if($_GET['action'] == "update"){
	foreach($_POST['check'] as $key => $value){
		$lncln->tag($key, $_POST['images'][$key]['tags']);
		$lncln->caption($key, $_POST['images'][$key]['caption']);
		$lncln->changeAlbum($key, $_POST['images'][$key]['album']);
		$lncln->obscene($key, $_POST['images'][$key]['obscene']);
	}
}

$lncln->img();

require_once(ABSPATH . "includes/header.php");

echo $lncln->prevNext();

$img = $_GET['img'] != '' ? "&amp;img=" . $_GET['img'] : "";
 
?>
<form action="moderate.php?action=update<?=$img;?>" method="post">
<?
	foreach($lncln->images as $image):	
		$tags = join(', ', $image['tags']);
?>
	<div id="<?=$image['id'];?>" class="modDiv">
		<input type="checkbox" name="check[<?=$image['id'];?>]" id="check<?=$image['id'];?>" /><br />
		Obscene: <select name="images[<?=$image['id'];?>][obscene]" onfocus="modCheck('<?=$image['id'];?>')">
				<?if($image['obscene'] == 1):?>
					<option value="1" selected>Yes</option><option value="0">No</option>
				<?else:?>
					<option value="1">Yes</option><option value="0" selected>No</option>
				<?endif;?>
				 </select> 
		<!--<input type="checkbox" name="images[<?=$image['id'];?>][obscene]" />-->
		<a href="<?=URL;?>images/full/<?=$image['file'];?>" target="_blank" class="modImage"><img src="<?=URL;?>images/thumb/<?=$image['file'];?>" /></a>
		<div class="modForms">
			<input type="hidden" name="images[<?=$image['id'];?>][id]" value="<?=$image['id'];?>" /><br />
			Tags:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name='images[<?=$image['id'];?>][tags]' value="<?=$tags;?>" onfocus="modCheck('<?=$image['id'];?>')"/><br />
			Caption:&nbsp;<textarea name="images[<?=$image['id'];?>][caption]" rows="10" cols="50" wrap="off" onfocus="modCheck('<?=$image['id'];?>')"><?=$image['caption'];?></textarea><br />
			Album:&nbsp;&nbsp;&nbsp;
			<select name="images[<?=$image['id'];?>][album]" onfocus="modCheck('<?=$image['id'];?>')">
				<option value="0">No album</option>
	<?foreach($lncln->getAlbums() as $album):?>
		<?$selected = $album['name'] == $image['album'] ? "selected" : "";?>
				<option value="<?=$album['id'];?>" <?=$selected;?>><?=$album['name'];?></option>
	<?endforeach;?>
			</select>
		</div>
	</div>
<?
	endforeach;
?>
	<input type="submit" value="Submit" />
</form>
<div id='bPrevNext'>
<?
	echo $lncln->prevNext();
?>
</div>	
<?


require_once(ABSPATH . "includes/footer.php");

?>
