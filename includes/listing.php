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

$action = $lncln->script == "image.php" ? URL . $lncln->script . "?img=" . $lncln->page . $lncln->extra : URL . $lncln->script . "?page=" . $lncln->page . $lncln->extra;

foreach ($lncln->images as $image){
	if($lncln->checkSmall($image['obscene'], $image['small'])){
		$link = "javascript:badImage('" . $image['id'] . "');";
	}
	else{
		$link = URL . "image/" . $image['id'];
	}
	
	$date = $_SESSION['thumbnail'] == true ? date('m/d/Y', $image['postTime'] + 3 * 60 * 60) : date('m/d/Y - h:i:s A', $image['postTime'] + 3 * 60 * 60);
?>

	<div class="<?echo $lncln->type;?>">
		<a href="<?echo $link;?>" id="l<?echo $image['id'];?>" name="<?echo $image['id'];?>"><?echo $image['id'];?></a>
		<?foreach($lncln->modules as $module){
			if(method_exists($module, "above")){
				echo $module->above($image['id'], $action);
			}
		}?>
		Posted: <?=$date;?> 

		<div class="imageLink" >
<?	if($image['obscene'] == 1):?>
			This has been voted obscene.<br />
<?	endif;?>
<?	if(!$_GET['thumb'] && $image['type'] == 'gif'):?>
			This is a gif.<br />
<?	endif;?>
<?	if($image['postTime'] > time()):?>
			This is not on the homepage yet.<br />
<?	endif;?>
<?	if($lncln->checkSmall($image['obscene'], $image['small'])):?>
			<div class="badImage" id="b<?echo $image['id'];?>">
<?	endif;?>
			<a name="<?echo $image['id'];?>" href="<?=URL;?>images/full/<?echo $image['file'];?>" target="_blank"><img src="<?=URL;?>images/<?echo $lncln->type;?>/<?echo $image['file'];?>" alt="<?echo $image['id'];?>" /></a>
<?	//don't show underImage() if in thumbnails
	if(!$_SESSION['thumbnail']):
		/**
		 * Main part of the script right here 
		 */
		foreach($lncln->modules as $module){
			if(method_exists($module, "below")){
				echo $module->below($image['id'], $action);
			}
		}
	else:
		echo "\n\t\t\t<br />\n";
	endif;?>
<?	if($lncln->user->permissions['report'] == 1):?>
			<a href="<?echo URL;?>report.php?img=<?echo $image['id'];?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/report.png" alt="Report Image" title="Report Image" style='border: none;'/></a>
<?	endif;?>
<?	foreach($lncln->modules as $module){
		if(method_exists($module, "icon")){
			echo $module->icon($image['id'], $action);
		}
	}?>
<?	if($lncln->user->permissions['obscene'] == 1):?>
			<a href="<?=$action;?>&amp;obscene=<?echo $image['id'];?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/obscene.png" alt="Obscene" title="Obscene" style='border: none;'/></a>
<?	endif;?>
<?	if($lncln->user->permissions['refresh'] == 1):?>
			<a href="<?=$action;?>&amp;refresh=<?echo $image['id'];?>" onclick="return confirm('Are you sure you want to refresh?');"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/refresh.png" alt="Refresh" title="Refresh" style='border: none;'/></a>
<?	endif;?>
<?	if($lncln->user->permissions['delete']):?>
			<a href="<?=$action;?>&amp;delete=<?echo $image['id'];?>" onclick="return confirm('Are you sure you want to delete this?');"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/delete.png" alt="Delete" title="Delete" style='border: none;'/></a>
<?	endif;?>
<?	if($lncln->checkSmall($image['obscene'], $image['small'])):?>
			</div>
<?	endif;?>
		</div>
	</div>
	<br />
<?}?>