<?
/**
 * moderate.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

require_once("../load.php");

$lncln = new lncln("index");

require_once(ABSPATH . "includes/iconActions.php");

$lncln->img();

require_once(ABSPATH . "includes/header.php");

if($_GET['action'] == "update")
	print_r($_POST);
	
if($lncln->isAdmin):
	echo $lncln->prevNext();

?>
	<form action="moderate.php?action=update" method="post">
<?
	foreach($lncln->images as $image):	
		$tags = join(', ', $image['tags']);
	?>
		<div id="<?=$image['id'];?>" class="modDiv">
			<input type="checkbox" name="check[]" id="check<?=$image['id'];?>" /> 
			<a href="<?=URL;?>images/full/<?=$image['file'];?>" target="_blank" class="modImage"><img src="<?=URL;?>images/thumb/<?=$image['file'];?>" /></a>
			<div class="modForms">
				<input type="hidden" name="images[][id]" value="<?=$image['id'];?>" /><br />
				Tags:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name='images[][tags]' value="<?=$tags;?>" onfocus="modCheck('<?=$image['id'];?>')"/><br />
				Caption:&nbsp;<textarea name="images[][caption]" rows="10" cols="50" wrap="off" onfocus="modCheck('<?=$image['id'];?>')"><?=$image['caption'];?></textarea><br />
				Album:&nbsp;&nbsp;&nbsp;
				<select name="images[][album]" onfocus="modCheck('<?=$image['id'];?>')">
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
endif;


require_once(ABSPATH . "includes/footer.php");

?>
