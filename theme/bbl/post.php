<?php
/**
 * post.php
 * 
 * The page that actually displays the images.  This includes the icons.
 * Runs through $lncln-images for all image data and prints it
 * Another very important page.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

$small = $lncln->check_small($image['id']);

if($small){
	$link = "javascript:show_image('" . $image['id'] . "');";
}
else{
	$link = URL . "image/" . $image['id'];
}

$date = $_SESSION['thumbnail'] == true ? 
		date('m/d/Y', $image['postTime'] + 3 * 60 * 60) : 
		date('m/d/Y - h:i:s A', $image['postTime'] + 3 * 60 * 60);

?>

	<div class="<?= $lncln->type;?> content">
		<span class='image_id'>
			<a href="<?echo $link;?>" id="l<?echo $image['id'];?>" name="<?echo $image['id'];?>">
				<?echo $image['id'];?>
			</a>
		</span>
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
if($small):?>
		<div class="badImage" id="b<?echo $image['id'];?>">
<?endif;?>
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
endif;
foreach($lncln->modules as $module){
	if(method_exists($module, "icon")){
		echo $module->icon($image['id']) . "\n";
	}
}

if($small):?>
		</div>
<?endif;
	echo "\t</div>";
