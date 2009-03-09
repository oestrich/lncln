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

if($lncln->isAdmin):
	echo $lncln->prevNext();

?>
	<form action="moderate.php?action=update" method="post">
<?
	foreach($lncln->images as $image):	
		$tags = join(', ', $image['tags']);
	?>
		<div id="<?=$image['id'];?>" class="modDiv">
			<input type="checkbox" name="check<?=$image['id'];?>" id="check<?=$image['id'];?>" /> 
			<a href="<?=URL;?>images/full/<?=$image['file'];?>" target="_blank" class="modImage"><img src="<?=URL;?>images/thumb/<?=$image['file'];?>" /></a>
			<div class="modForms">
				<input type="hidden" name="id" value="<?=$image['id'];?>" /><br />
				Tags:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name='tags' value="<?=$tags;?>" onchame="modCheck(''<?=$image['id'];?>'')"/><br />
				Caption:&nbsp;<textarea name="caption" rows="10" cols="50" wrap="off" onchame="modCheck(''<?=$image['id'];?>'')"><?=$image['caption'];?></textarea><br />
				Album:&nbsp;&nbsp;&nbsp;
				<select name="album" onchame="modCheck(''<?=$image['id'];?>'')">
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
