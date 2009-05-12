<?
/**
 * header.php
 * 
 * This should be included in every file before output is to be sent.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title><?echo $lncln->display->settings['title'];?></title>

	<link rel="alternate" type="application/rss+xml" title="All Images" href="rss/all" />
	<link rel="alternate" type="application/rss+xml" title="Safe Images" href="rss/safe" />
	<link type="text/css" rel="stylesheet" href="<?echo URL;?>theme/<?echo THEME;?>/style.css" />
	<script type="text/javascript" src="<?echo URL;?>includes/javascript.js" >
	</script>
</head>
<body onload="init()">
	<input type="hidden" id="URL" value="<?=URL;?>" />
	<div id="container">
		<div id="header">
			<a href="<?echo URL;?>index/" ><img src="<?echo URL;?>theme/<?echo THEME;?>/images/abe.png" alt="Abe" id="abeLink" /></a>
			<div id="navBar">
				<div class="headerRow">
					<a href='<?echo URL;?>index.php'>Newest</a>
<?
	$thumb = $_GET['thumb'] ? "" : "&amp;thumb=true";
?>
					<a href='<?echo URL;?>thumbnail/<?=$_SESSION['thumbail'] == true ? "off" : "on";?>'>Thumbnail view</a>
<?
foreach($lncln->display->rows[1] as $module){
	echo $lncln->modules[$module]->headerLink();
}

if($lncln->user->isUser == true){
?>
					<a href='<?echo URL;?>logout.php'>Log out <?echo $lncln->user->username;?></a>
					<a href='<?echo URL;?>user.php'>Change Settings</a>
<?}
else{?>
					<a href='<?echo URL;?>login.php'>Log in</a>
					<?if($lncln->display->settings['register'] == 1):?>
						<a href='<?echo URL;?>register.php'>Register</a>
					<?endif;?>
					<a href='<?echo URL;?>index.php?viewObscene=true'>View Obscene</a> (<?=$_COOKIE['obscene'] ? "On" : "Off";?>)
<?}?>
				</div>
				<div class="headerRow">
	<?if($lncln->user->permissions['upload'] == 1):?>
					Upload: <a href="javascript:;" onmousedown="toggleDiv('regular')">File</a> <a href="javascript:;" onmousedown="toggleDiv('url')">URL</a>
	<?endif;?> 
<?
	$sql = "SELECT COUNT(*) FROM images WHERE queue = 0 AND postTime <= " . time();
	$result = mysql_query($sql);
	$result = mysql_fetch_assoc($result);
?>
					We have <?echo $result['COUNT(*)'];?> images.
	<?if($lncln->user->permissions['upload'] == 1):?>
					<!-- upload form -->
					<form enctype="multipart/form-data" action="<?=URL;?>upload.php" method="post"  id="form" style="display: none;">
						<div>
							<input type="hidden" name="type" id="formType" value="regular" />
							Uploaded files will be moderated<br />
							<input name="upload0" id="upload0" type="file" />
							<br />
							<input name="upload1" id="upload1" type="file" />
							<br />
							<input name="upload2" id="upload2" type="file" />
							<br />
							<input name="upload3" id="upload3" type="file" />
							<br />
							<input name="upload4" id="upload4" type="file" />
							<br />
							<input name="upload5" id="upload5" type="file" />
							<br />
							<input name="upload6" id="upload6" type="file" />
							<br />
							<input name="upload7" id="upload7" type="file" />
							<br />
							<input name="upload8" id="upload8" type="file" />
							<br />
							<input name="upload9" id="upload9" type="file" />
							<br />
							<input type="submit" value="Upload File" />
							<br />
							Max total upload size: <?=ini_get("upload_max_filesize");?>
						</div>
					</form>
	<?endif;?>
				</div>
<?	
if($lncln->user->permissions['isAdmin'] == 1){
	$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
?>
				<div class="headerRow">
					Admin: 
					<a href="<?=URL;?>admin/">Admin Panel</a>
					<a href='<?echo URL;?>admin/queue.php'>Check the Queue (<?echo $row['COUNT(*)'];?>)</a>
				</div>
<?}?>	
				<div class="headerRow">
<?
foreach($lncln->display->rows[2] as $module){
	echo $lncln->modules[$module]->headerLink();
}
?>
				</div>
			</div>
		</div>
		<div id="mainBody">
			<br />
			<?if($lncln->user->permissions['isAdmin'] == 1  && $lncln->moderationOn):?>
				<div style="text-align: center; left: -50px; position: relative; z-index: 1; padding-bottom: 20px;"><a href="<?=URL;?>admin/moderate.php">Moderate Images</a></div>
			<?endif;?>	
