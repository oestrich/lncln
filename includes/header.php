<?
/**
 * header.php
 * 
 * This should be included in every file before output is to be sent.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
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
	<title><?echo TITLE;?></title>

	<link rel="alternate" type="application/rss+xml" title="All Images" href="rss/all.rss" />
	<link rel="alternate" type="application/rss+xml" title="Safe Images" href="rss/safe.rss" />
	<link type="text/css" rel="stylesheet" href="<?echo URL;?>theme/<?echo THEME;?>/style.css" />
	<script type="text/javascript" src="<?echo URL;?>includes/javascript.js" >
	</script>
</head>
<body>
	<div id="container">
		<div id="header">
			<a href="<?echo URL;?>index.php" ><img src="<?echo URL;?>theme/<?echo THEME;?>/images/abe.png" alt="Abe" id="abeLink" /></a>
			<div id="navBar">
				<a href='<?echo URL;?>index.php'>Newest</a>
<?
	if($_GET['thumb']){
		$thumb = "";
		$onOff = "Off";
	}
	else{
		$thumb = "&amp;thumb=true";
		$onOff = "On";
	}
?>
				<a href='<?echo URL;?>index.php?img=<?echo $lncln->firstImage . $thumb;?>'>Thumbnail view (<?echo $onOff;?>)</a>
				<a href='<?echo URL;?>album.php'>Albums</a>
<?
if($lncln->user->isUser){
?>
				<a href='<?echo URL;?>logout.php'>Log out <?echo $lncln->user->username;?></a>
				<a href='<?echo URL;?>user.php'>Change Settings</a>
<?
}
else{
?>
				<a href='<?echo URL;?>login.php'>Log in</a>
				All images will be directed to the queue.
<?
}

if($lncln->user->permissions['isAdmin'] == 1){
	$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
	$result = mysql_query($sql);
	$result = mysql_fetch_assoc($result);

?>
				<a href='<?echo URL;?>admin/queue.php'>Check the Queue (<?echo $result['COUNT(*)'];?>)</a>
				<a href='<?echo URL;?>admin/adduser.php'>Add a user</a>
				<a href='<?echo URL;?>admin/albums.php'>Edit Albums</a>
<?
}
?>
				<br />
				Upload: <a href="javascript:;" onmousedown="toggleDiv('regular')">File</a> <a href="javascript:;" onmousedown="toggleDiv('url')">URL</a>
				
				
				<!-- upload form -->
				<form enctype="multipart/form-data" action="<?=URL;?>upload.php" method="post"  id="form" style="display: none;">
					<div>
						<input type="hidden" name="type" id="formType" value="regular" />
						Uploaded files will be moderated<br />
						<input name="upload0" id="upload0" type="file" onchange="TestFileType(this.form.upload0.value, ['', 'gif', 'jpg', 'png']);"/>
						<br />
						<input name="upload1" id="upload1" type="file" onchange="TestFileType(this.form.upload1.value, ['', 'gif', 'jpg', 'png']);"/>
						<br />
						<input name="upload2" id="upload2" type="file" onchange="TestFileType(this.form.upload2.value, ['', 'gif', 'jpg', 'png']);"/>
						<br />
						<input name="upload3" id="upload3" type="file" onchange="TestFileType(this.form.upload3.value, ['', 'gif', 'jpg', 'png']);"/>
						<br />
						<input name="upload4" id="upload4" type="file" onchange="TestFileType(this.form.upload4.value, ['', 'gif', 'jpg', 'png']);"/>
						<br />
						<input name="upload5" id="upload5" type="file" onchange="TestFileType(this.form.upload5.value, ['', 'gif', 'jpg', 'png']);"/>
						<br />
						<input name="upload6" id="upload6" type="file" onchange="TestFileType(this.form.upload6.value, ['', 'gif', 'jpg', 'png']);"/>
						<br />
						<input name="upload7" id="upload7" type="file" onchange="TestFileType(this.form.upload7.value, ['', 'gif', 'jpg', 'png']);"/>
						<br />
						<input name="upload8" id="upload8" type="file" onchange="TestFileType(this.form.upload8.value, ['', 'gif', 'jpg', 'png']);"/>
						<br />
						<input name="upload9" id="upload9" type="file" onchange="TestFileType(this.form.upload9.value, ['', 'gif', 'jpg', 'png']);"/>
						<br />
						<input type="submit" value="Upload File" />
						<br />
						Max total upload size: <?=ini_get("upload_max_filesize");?>
					</div>
				</form>
	
				<br />
				<a href='<?echo URL;?>index.php?viewObscene=true'>View Obscene</a>
<?
				echo $_COOKIE['obscene'] == 1 ? "You are viewing obscene content." : "You are not viewing obscene content.";
	
	if(file_exists("bblincoln-latest.tar.gz")){
?>
				<br />
				<a href="<?echo URL;?>bblincoln-latest.tar.gz">Download them all</a> Updates at 1AM PST.
<?	
	}
?>
				<br />
				<form id='search' enctype="multipart/form-data" action="<?echo URL;?>search.php" method="get">
					<div>
						Tag search:
						<input type='text' name='search' />
						<input type='submit' value='Search' />
					</div>
				</form>
			</div>
		</div>
		<div id="mainBody">
			<br />
			<?if($lncln->user->permissions['isAdmin'] == 1  && $lncln->moderationOn):?>
				<div style="text-align: center; left: -50px; position: relative; z-index: 1;"><a href="<?=URL;?>admin/moderate.php">Moderate Images</a></div>
			<?endif;?>	
