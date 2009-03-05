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
?>
	<form action="moderate.php?action=update" method="post">
<?
	foreach($lncln->images as $image):	
		$tags = join(', ', $image['tags']);
	?>
		<div id="<?=$image['id'];?>" class="modDiv">
			<a href="<?=URL;?>images/full/<?=$image['file'];?>" target="_blank" class="modImage"><img src="<?=URL;?>images/thumb/<?=$image['file'];?>" /></a>
			<div class="modForms">
				<input type="hidden" name="id" value="<?=$image['id'];?>" /><br />
				<input type="text" name='tags' value="<?=$tags;?>" /><br />
				<textarea name="caption" rows="10" cols="50" wrap="off"><?=$image['caption'];?></textarea><br />
				<select name="album">
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
<?
endif;


require_once(ABSPATH . "includes/footer.php");

?>
