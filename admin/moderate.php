<?
/**
 * moderate.php
 * 
 * Easily moderate many images at a time.  Bulk editing has never been easier!
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

require_once("../load.php");

$lncln->index();

include_once("admin.php");

if($_GET['action'] == "update"){
	if(!is_array($_POST['check']))
		$_POST['check'] = array();
	
	foreach($_POST['check'] as $key => $value){
		$lncln->tag($key, $_POST['images'][$key]['tags']);
		$lncln->caption($key, $_POST['images'][$key]['caption']);
		$lncln->changeAlbum($key, $_POST['images'][$key]['album']);
		$lncln->obscene($key, $_POST['images'][$key]['obscene']);
	}
}

$lncln->img();

include_once(ABSPATH . "includes/header.php");

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
			<table>
				<tr>
					<td>Tags:</td>
					<td><?=createInput($lncln->modules['tags']->moderate($image['id']), $image['id'], "onfocus=\"modCheck('" . $image['id'] . "')\"");?></td>
				</tr>
				<tr>
					<td>Caption:</td>
					<td><textarea name="images[<?=$image['id'];?>][caption]" rows="10" cols="50" wrap="off" onfocus="modCheck('<?=$image['id'];?>')"><?=$image['caption'];?></textarea></td>
				</tr>
				<tr>
					<td>Album:</td>
					<td><select name="images[<?=$image['id'];?>][album]" onfocus="modCheck('<?=$image['id'];?>')">
							<option value="0">No album</option>
				<?foreach($lncln->getAlbums() as $album):?>
					<?$selected = $album['name'] == $image['album'] ? "selected" : "";?>
							<option value="<?=$album['id'];?>" <?=$selected;?>><?=$album['name'];?></option>
				<?endforeach;?>
						</select>
					</td>
				</tr>
			</table>
			<!--Tags:<input type="text" name='images[<?=$image['id'];?>][tags]' value="<?=$tags;?>" onfocus="modCheck('<?=$image['id'];?>')"/><br />-->
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


include_once(ABSPATH . "includes/footer.php");

?>
