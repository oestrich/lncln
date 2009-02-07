<?
/*
/	lncln by Eric Oestrich
/	Version .5
/
*/


foreach ($images as $image){
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

	<div class="<?echo $type;?>">
		<a href="<?echo $link;?>" id="l<?echo $image['id'];?>" name="<?echo $image['id'];?>"><?echo $image['id'];?></a> Rating : <?echo $image['rating'];?> Posted : <?echo $date;
?>

		<div class="imageLink" >
<?
	if($image['obscene'] == 1){
?>
			This has been voted obscene.<br />
<?
	}
	if(!$_GET['thumb'] && $image['type'] == 'gif'){
?>
			This is a gif.<br />
<?
	}
	
	if($image['postTime'] > time()){
?>
			This is not on the homepage yet.<br />
<?
	}
	
	if($image['obscene'] == 1 && (!$_COOKIE['obscene'] || !isset($_COOKIE['obscene']))){
?>
			<div class="obscene" id="i<?echo $image['id'];?>">
<?
	}
	if($image['rating'] <= -10){
?>
			<div class="badImage" id="b<?echo $image['id'];?>">
<?
	}
?>
			<a name="<?echo $image['id'];?>" href="img/<?echo $image['file'];?>"><img src="<?echo $type;?>/<?echo $image['file'];?>" alt="<?echo $image['id'];?>" /></a>
<?	

	//don't show caption if in thumbnails
	if(!$_GET['thumb']){
		//caption stuff
		if($isAdmin){
			$class = "class='captionAdmin'";
			$onClick = "onclick=\"caption('" . $image['id'] . "')\"";
		}
		else if($isLoggedIn){
			$onClick = "onclick=\"caption('" . $image['id'] . "')\"";
			$class = "class='caption'";
		}
		else{
			$onClick = "";
			$class = "";
		}
		
?>
			<div id="caption<?echo $image['id'];?>" <?echo $onClick; echo $class;?>>
<?
		if($image['caption'] == ""){
?>
				No Caption.
<?
		}
		else{
?>
				<?echo $image['caption'];?> 
<?
		}
?>
			</div>
<?
		if(($isLoggedIn && $image['caption'] == "") || $isAdmin){
?>
			<form id="c<?echo $image['id'];?>" style="display:none;" enctype="multipart/form-data" action="index.php?caption=true&amp;img=<?echo $start . $extra;?>#<?echo $image['id'];?>" method="post">
				<input type="hidden" name="id" value="<?echo $image['id'];?>" />
				<textarea name="caption" rows="6" cols="40" id='formCaption<?echo $image['id'];?>'><?echo $image['caption'];?></textarea>
				<input type="submit" value="Caption!" />
			</form>
<?
		}

		//tags
		$tags = join(', ', $image['tags']);
		$tagsForm = $tags;
		if($tags == ""){
			$tags = "None.";
		}
		
		if($isLoggedIn){
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
<?
		if($isLoggedIn || $isAdmin){
?>
			<form id="t<?echo $image['id'];?>" style="display:none;" enctype="multipart/form-data" action="index.php?tag=true&amp;img=<?echo $start . $extra;?>#<?echo $image['id'];?>" method="post">
				<div>
					<input type="hidden" name="id" value="<?echo $image['id'];?>" />
					Split tags with a ','.<br />
					<input name="tags" id='formTag<?echo $image['id'];?>' value='<?echo $tagsForm;?>' size='85'/>
					<input type="submit" value="Tag it!" />
				</div>
			</form>
<?		}
	}	
	//links!
	if($_GET['thumb']){
?>
			<br />
<?
}
?>
			<a href="report.php?img=<?echo $image['id'];?>"><img src="images/report.png" alt="Report Image" title="Report Image" style='border: none;'/></a>
<?
	if($isLoggedIn){
?>
			<a href="index.php?rateUp=<?echo $image['id'];?>&amp;img=<?echo $start . $extra;?>"><img src="images/up.png" alt="Up" title="Up" style='border: none;'/></a>
			<a href="index.php?rateDown=<?echo $image['id'];?>&amp;img=<?echo $start . $extra;?>"><img src="images/down.png" alt="Down" title="Down" style='border: none;'/></a>
			<a href="index.php?obscene=<?echo $image['id'];?>&amp;img=<?echo $start . $extra;?>"><img src="images/obscene.png" alt="Obscene" title="Obscene" style='border: none;'/></a>
			<a href="index.php?refresh=<?echo $image['id'];?>&amp;img=<?echo $start . $extra;?>" onclick="return confirm('Are you sure you want to refresh?');"><img src="images/refresh.png" alt="Refresh" title="Refresh" style='border: none;'/></a>
<?
	}	
	if($isAdmin){
?>
			<a href="index.php?delete=<?echo $image['id'];?>&amp;img=<?echo $start . $extra;?>"><img src="images/delete.png" alt="Delete" title="Delete" style='border: none;'/></a>
<?
	}
	
	if($image['obscene'] == 1 && (!$_COOKIE['obscene'] || !isset($_COOKIE['obscene']))){
?>
			</div>
<?
	}
	if($image['rating'] <= -10){
?>
			</div>
<?
	}
?>
		</div>
	</div>
	
<?
}
?>