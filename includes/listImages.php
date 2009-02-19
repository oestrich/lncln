<?
/**
 * listImages.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.6.1 $Id$
 * 
 * @package lncln
 */


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

			<a name="<?echo $image['id'];?>" href="images/full/<?echo $image['file'];?>" target="_blank"><img src="images/<?echo $lncln->type;?>/<?echo $image['file'];?>" alt="<?echo $image['id'];?>" /></a>
<?	
	//don't show caption if in thumbnails
	if(!$_GET['thumb']):
		//caption stuff
		if($lncln->isAdmin){
			$class = "class='captionAdmin'";
			$onClick = "onclick=\"caption('" . $image['id'] . "')\"";
		}
		else if($lncln->isLoggedIn){
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

		<?if(($lncln->isLoggedIn && $image['caption'] == "") || $lncln->isAdmin):?>
			<form id="c<?echo $image['id'];?>" style="display:none;" enctype="multipart/form-data" action="<?echo $lncln->script;?>?caption=true&amp;img=<?echo $lncln->firstImage;?>" method="post">
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
		
		if($lncln->isLoggedIn){
			$classTag = "class='tag'";
		}
		else{
			$classTag = "";
		}
?>
			<div id='tag<?echo $image['id'];?>' <?echo $classTag;?> onclick="tag('<?echo $image['id'];?>');">
				Tags: <?echo $tags;?> 
				<br />
			</div>

		<?if($lncln->isLoggedIn || $lncln->isAdmin):?>
			<form id="t<?echo $image['id'];?>" style="display:none;" enctype="multipart/form-data" action="<?echo $lncln->script;?>?tag=true&amp;img=<?echo $lncln->firstImage;?>" method="post">
				<div>
					<input type="hidden" name="id" value="<?echo $image['id'];?>" />
					Split tags with a ','.<br />
					<input name="tags" id='formTag<?echo $image['id'];?>' value='<?echo $tagsForm;?>' size='85'/>
					<input type="submit" value="Tag it!" />
				</div>
			</form>
		<?endif;?>

	<?endif;?>

	<?if($_GET['thumb']):?>
			<br />
	<?endif;?>

			<a href="<?echo URL;?>report.php?img=<?echo $image['id'];?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/report.png" alt="Report Image" title="Report Image" style='border: none;'/></a>

	<?if($lncln->isLoggedIn):?>
			<a href="<?echo URL; echo $lncln->script;?>?rateUp=<?echo $image['id'];?>&amp;img=<?echo $lncln->firstImage . $lncln->extra;?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/up.png" alt="Up" title="Up" style='border: none;'/></a>
			<a href="<?echo URL; echo $lncln->script;?>?rateDown=<?echo $image['id'];?>&amp;img=<?echo $lncln->firstImage . $lncln->extra;?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/down.png" alt="Down" title="Down" style='border: none;'/></a>
			<a href="<?echo URL; echo $lncln->script;?>?obscene=<?echo $image['id'];?>&amp;img=<?echo $lncln->firstImage . $lncln->extra;?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/obscene.png" alt="Obscene" title="Obscene" style='border: none;'/></a>
			<a href="<?echo URL; echo $lncln->script;?>?refresh=<?echo $image['id'];?>&amp;img=<?echo $lncln->firstImage . $lncln->extra;?>" onclick="return confirm('Are you sure you want to refresh?');"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/refresh.png" alt="Refresh" title="Refresh" style='border: none;'/></a>
	<?endif;?>

	<?if($lncln->isAdmin):?>
			<a href="<?echo $lncln->script;?>?delete=<?echo $image['id'];?>&amp;img=<?echo $lncln->firstImage . $lncln->extra;?>"><img src="<?echo URL;?>theme/<?echo THEME;?>/images/delete.png" alt="Delete" title="Delete" style='border: none;'/></a>
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
	
<?
}
?>