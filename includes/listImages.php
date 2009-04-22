<?
/**
 * listImages.php
 * 
 * The page that actually displays the images.  This includes the icons.
 * Runs through $lncln-images for all image data and prints it
 * Another very important page.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

if($lncln->page == 0 && $lncln->maxPage == 0){
	echo "<br /><br />No images.<br />";
}

$action = $lncln->script == "image.php" ? URL . $lncln->script . "?img=" . $lncln->page . $lncln->extra : URL . $lncln->script . "?page=" . $lncln->page . $lncln->extra;

foreach ($lncln->images as $image){
	if($image['obscene'] == 1 && (!$_COOKIE['obscene'] || !isset($_COOKIE['obscene']))){
		if($image['obscene'] && $image['rating'] <= -10){
			$link = "javascript:both('" . $image['id'] . "');";
		}
		else if($image['rating'] <= -10){
			$link = "javascript:badImage('" . $image['id'] . "');";
		}
		else if($image['obscene']){
			$link = "javascript:obscene('". $image['id'] . "');";
		}
		else{
			$link = "image.php?img=" . $image['id'];
		}
	}
	else{
		$link = "image.php?img=" . $image['id'];
	}
	
	if($_GET['thumb']){
		$date = date('m/d/Y', $image['postTime'] + 3 * 60 * 60);
	}
	else{
		$date = date('m/d/Y - h:i:s A', $image['postTime'] + 3 * 60 * 60);
	}

?>

	<div class="<?echo $lncln->type;?>">
		<a href="<?echo $link;?>" id="l<?echo $image['id'];?>" name="<?echo $image['id'];?>"><?echo $image['id'];?></a> Rating: <?echo $image['rating'];?> Posted: <?=$date;?> 

		<div class="imageLink" >

	<?if($image['obscene'] == 1):?>
			This has been voted obscene.<br />
	<?endif;?>

	<?if(!$_GET['thumb'] && $image['type'] == 'gif'):?>
			This is a gif.<br />
	<?endif;?>

	<?if($image['postTime'] > time()):?> 
			This is not on the homepage yet.<br />
	<?endif;?>

	<?if($image['obscene'] == 1 && (!$_COOKIE['obscene'] || !isset($_COOKIE['obscene']))):?>
			<div class="obscene" id="i<?echo $image['id'];?>">
	<?endif;?>

	<?if($image['rating'] <= -10):?>
			<div class="badImage" id="b<?echo $image['id'];?>">
	<?endif;?>

			<a name="<?echo $image['id'];?>" href="<?=URL;?>images/full/<?echo $image['file'];?>" target="_blank"><img src="<?=URL;?>images/<?echo $lncln->type;?>/<?echo $image['file'];?>" alt="<?echo $image['id'];?>" /></a>
<?	
	//don't show caption if in thumbnails
	if(!$_GET['thumb']):
		//caption stuff
		if(($lncln->user->isUser && $image['caption'] == "") || $lncln->user->permissions['isAdmin']){
			$onClick = "onclick=\"caption('" . $image['id'] . "')\"";
			$class = "class='caption'";
		}
		else{
			$onClick = "";
			$class = "";
		}
		
?>
			<div id="caption<?echo $image['id'];?>" <?echo $onClick; echo $class;?>>

		<?if($image['caption'] == ""):?>
				No Caption.
		<?else:?>
				<?=$image['caption'];?> 
		<?endif;?>

			</div>
		<?if(($lncln->user->isUser && $image['caption'] == "") || $lncln->user->permissions['isAdmin']):?>
			<form id="c<?echo $image['id'];?>" style="display:none;" enctype="multipart/form-data" action="<?echo $action;?>&amp;caption=true" method="post">
				<input type="hidden" name="id" value="<?echo $image['id'];?>" />
				<textarea name="caption" rows="6" cols="40" id='formCaption<?echo $image['id'];?>'><?echo $image['caption'];?></textarea>
				<input type="submit" value="Caption!" />
			</form>
		<?endif;?>
<?
		//tags
		$tags = join(', ', $image['tags']);
		$tagsForm = $tags;
		if($tags == ""){
			$tags = "None.";
		}
		
		if($lncln->user->isUser){
			$classTag = "class='tag'";
			$onClick = "onclick=\"tag('" . $image['id'] . "');\"";
		}
		else{
			$classTag = "";
			$onClick = "";
		}
?>
			<div id='tag<?echo $image['id'];?>' <?echo $classTag;?> <?=$onClick;?>>
				Tags: <?echo $tags;?> 
				<br />
			</div>
		<?if($lncln->user->isUser):?>
			<form id="t<?echo $image['id'];?>" style="display:none;" action="<?echo $action;?>&amp;tag=true" method="post">
				<div>
					<input type="hidden" name="id" value="<?echo $image['id'];?>" />
					Split tags with a ','.<br />
					<input name="tags" id='formTag<?echo $image['id'];?>' value='<?echo $tagsForm;?>' size='85'/>
					<input type="submit" value="Tag it!" />
				</div>
			</form>
		<?endif;?>

<?
		if(($lncln->user->isUser && $image['album'] == "No Album") || $lncln->user->permissions['isAdmin']){
			$class = "class='album'";
			$onClick = "onclick=\"album('". $image['id'] . "');\"";
		}
		else{
			$class = "";
			$onClick = "";
		}

?>
			<div id='album<?echo $image['id'];?>' <?=$class;?> <?=$onClick;?>>
				Album: <?=$image['album'];?>
			</div>
		<?if(($lncln->user->isUser && $image['album'] == "No Album") || $lncln->user->permissions['isAdmin'] == 1):?>
			<form id="a<?echo $image['id'];?>" style="display:none;" action="<?echo $action;?>&amp;action=album" method="post">
				<div>
					<input type="hidden" name="id" value="<?echo $image['id'];?>" />
					<select name="album">
						<option value="0">No album</option>
			<?foreach($lncln->getAlbums() as $album):?>
				<?$selected = $album['name'] == $image['album'] ? "selected" : "";?>
						<option value="<?=$album['id'];?>" <?=$selected;?>><?=$album['name'];?></option>
			<?endforeach;?>
					</select>
					<input type="submit" value="Change album" />
				</div>
			</form>
		<?endif;?>
	<?endif;?>

	<?if($_GET['thumb']):?>
			<br />
	<?endif;?>

			<a href="<?echo URL;?>report.php?img=<?echo $image['id'];?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/report.png" alt="Report Image" title="Report Image" style='border: none;'/></a>

	<?if($lncln->user->permissions['rate'] == 1):?>
			<a href="<?=$action;?>&amp;rateUp=<?echo $image['id'];?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/up.png" alt="Up" title="Up" style='border: none;'/></a>
			<a href="<?=$action;?>&amp;rateDown=<?echo $image['id'];?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/down.png" alt="Down" title="Down" style='border: none;'/></a>
	<?endif;?>
	<?if($lncln->user->permissions['obscene'] == 1):?>
			<a href="<?=$action;?>&amp;obscene=<?echo $image['id'];?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/obscene.png" alt="Obscene" title="Obscene" style='border: none;'/></a>
	<?endif;?>
	<?if($lncln->user->permissions['refres'] == 1):?>
			<a href="<?=$action;?>&amp;refresh=<?echo $image['id'];?>" onclick="return confirm('Are you sure you want to refresh?');"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/refresh.png" alt="Refresh" title="Refresh" style='border: none;'/></a>
	<?endif;?>
	<?if($lncln->user->permissions['delete']):?>
			<a href="<?=$action;?>&amp;delete=<?echo $image['id'];?>" onclick="return confirm('Are you sure you want to delete this?');"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/delete.png" alt="Delete" title="Delete" style='border: none;'/></a>
	<?endif;?>

	<?if($image['obscene'] == 1 && (!$_COOKIE['obscene'] || !isset($_COOKIE['obscene']))):?>
			</div>
	<?endif;?>

	<?if($image['rating'] <= -10):?>
			</div>
	<?endif;?>

		</div>
	</div>
	<br />
<?}?>