<?
/**
 * listing.php
 * 
 * The page that actually displays the images.  This includes the icons.
 * Runs through $lncln-images for all image data and prints it
 * Another very important page.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

if($lncln->page == 0 && $lncln->maxPage == 0){
	echo "<br /><br />No images.<br />";
}

foreach ($lncln->images as $image){
	if($lncln->checkSmall($image['id'])){
		$link = "javascript:show_image('" . $image['id'] . "');";
	}
	else{
		$link = URL . "image/" . $image['id'];
	}
	
	$date = $_SESSION['thumbnail'] == true ? date('m/d/Y', $image['postTime'] + 3 * 60 * 60) : date('m/d/Y - h:i:s A', $image['postTime'] + 3 * 60 * 60);
?>

	<div class="<?= $lncln->type;?> content">
		<span class='image_id'><a href="<?echo $link;?>" id="l<?echo $image['id'];?>" name="<?echo $image['id'];?>"><?echo $image['id'];?></a></span>
		<br />
		<?=$date;?> 
		<?foreach($lncln->modules as $module){
			if(method_exists($module, "above")){
				echo $module->above($image['id']);
			}
		}
		
		echo "<br />";

	if($lncln->type != 'thumb' && $image['type'] == 'gif'):
			echo "This is a gif.<br />";
	endif;
	if($lncln->type != 'thumb' && $image['postTime'] > time()){
			echo "Not on the homepage yet.<br />\n";
	}
	if($lncln->checkSmall($image['id'])):?>
		<div class="badImage" id="b<?echo $image['id'];?>">
<?	endif;?>
		<div class='<?=$lncln->type;?>_image'>
			<a name="<?echo $image['id'];?>" href="<?=URL;?>images/full/<?echo $image['file'];?>" target="_blank" >
				<img src="<?=URL;?>images/<?echo $lncln->type;?>/<?echo $image['file'];?>" alt="<?echo $image['id'];?>" />
			</a>
		</div>
<?	//don't show underImage() if in thumbnails
	if(!$_SESSION['thumbnail']):
		/**
		 * Main part of the script right here 
		 */
		foreach($lncln->modules as $module){
			if(method_exists($module, "below")){
				echo $module->below($image['id']);
			}
		}
	else:
		echo "\t\t<br />\n";
	endif;?>
<?	if($lncln->user->permissions['report'] == 1):?>
		<a href="<?echo URL;?>report.php?img=<?echo $image['id'];?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/report.png" alt="Report Image" title="Report Image" style='border: none;'/></a>
<?	endif;?>
<?	foreach($lncln->modules as $module){
		if(method_exists($module, "icon")){
			echo $module->icon($image['id']);
		}
	}?>
<?	if($lncln->user->permissions['refresh'] == 1):?>
		<a href="<?=$action;?>&amp;refresh=<?echo $image['id'];?>" onclick="return confirm('Are you sure you want to refresh?');"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/refresh.png" alt="Refresh" title="Refresh" style='border: none;'/></a>
<?	endif;?>
<?	if($lncln->user->permissions['delete']):?>
		<a href="<?=$action;?>&amp;delete=<?echo $image['id'];?>" onclick="return confirm('Are you sure you want to delete this?');"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/delete.png" alt="Delete" title="Delete" style='border: none;'/></a>
<?	endif;?>
<?	if($lncln->checkSmall($image['id'])):?>
		</div>
<?	endif;
	echo "\t</div>";

}