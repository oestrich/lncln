<?
/**
 * header.php
 * 
 * This should be included in every file before output is to be sent.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
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
	<title><?echo $lncln->display->title;?></title>

	<link rel="alternate" type="application/rss+xml" title="All Images" href="<?=URL;?>rss/all" />
	<link rel="alternate" type="application/rss+xml" title="Safe Images" href="<?=URL;?>rss/safe" />
	<link type="text/css" rel="stylesheet" href="<?echo URL;?>theme/<?echo THEME;?>/style.css" />
	<?
		echo $lncln->display->include_css();
	?>
	<script type="text/javascript" src="<?echo URL;?>includes/lncln.js" >
	</script>
</head>
<body onload="init()">
	<form action="#" method="post">
		<div><input type="hidden" id="URL" value="<?=URL;?>" /></div>
	</form>
	<div id="container">
		<div id="header">
			<a href="<?echo URL;?>index/" ><img src="<?echo URL;?>theme/<?echo THEME;?>/images/abe.png" alt="Abe" id="abeLink" /></a>
			<div id="navBar">
				<div class="headerRow">
<?
foreach($lncln->display->rows[1] as $module){
	if(method_exists($module, "header_link")){
		echo $lncln->modules[$module]->header_link();
	}
}

if($lncln->user->isUser == true){
?>
					<a href='<?echo URL;?>logout/'>Log out <?echo $lncln->user->username;?></a>
					<a href='<?echo URL;?>user/'>Change Settings</a>
<?}
else{?>
					<a href='<?echo URL;?>login/'>Log in</a>
					<?if($lncln->display->settings['register'] == 1):?>
						<a href='<?echo URL;?>register/'>Register</a>
					<?endif;?>
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
					<form enctype="multipart/form-data" action="<?=URL;?>upload/" method="post"  id="form" style="display: none;">
						<div>
							<input type="hidden" name="type" id="formType" value="regular" />
							Uploaded files will be moderated<br />
<?		for($a = 0; $a < 10; $a++){
			echo "\t\t\t\t\t\t\t<input name='upload$a' id='upload$a' type='file' />\n";
			echo "\t\t\t\t\t\t\t<br />\n";
		}?>
							<input type="submit" value="Upload File" />
							<br />
							Max total upload size: <?=ini_get("upload_max_filesize");?>
						</div>
					</form>
	<?endif;?>
				</div>
				<div class="headerRow">
<?
foreach($lncln->display->rows[3] as $module){
	if(method_exists($module, "header_link")){
		echo $lncln->modules[$module]->header_link();
	}
}
?>
				</div>
				<div class="headerRow">
<?
foreach($lncln->display->rows[4] as $module){
	if(method_exists($module, "header_link")){
		echo $lncln->modules[$module]->header_link();
	}
}
?>
				</div>
			</div>
		</div>
		<div id="mainBody">
			<br />
			<?if($lncln->user->permissions['isAdmin'] == 1  && $lncln->moderationOn):?>
				<div style="text-align: center; position: relative; z-index: 1; padding-bottom: 20px;"><a href="<?=URL;?>admin/moderate.php">Moderate Images</a></div>
			<?endif;?>	
