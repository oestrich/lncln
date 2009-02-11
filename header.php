<?
/*
/	lncln by Eric Oestrich
/	Version .5
/
*/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title><?echo $config['title'];?></title>

	<link rel="alternate" type="application/rss+xml" title="All Images" href="rss/all.rss" />
	<link rel="alternate" type="application/rss+xml" title="Safe Images" href="rss/safe.rss" />
	<link type="text/css" rel="stylesheet" href="style.css" />
	<script type="text/javascript" src="javascript.js" >
	</script>
</head>
<body>
<div>
	<a href="javascript:;" onmousedown="toggleDiv('regular')">Upload</a> <a href="javascript:;" onmousedown="toggleDiv('url')">Upload from URL</a>
</div>

<!-- upload form -->
<form enctype="multipart/form-data" action="index.php?post=true" method="post"  id="form" style="display: none;">
	<div>
		<input type="hidden" name="type" id="formType" value="regular" />
		Uploaded files will be moderated. Tags are mandatory.<br />
		Obscene<br />
		<input name="upload0check" type="checkbox" value='true' class="obCheck"/>
		<input name="upload0" id="upload0" type="file" onchange="TestFileType(this.form.upload0.value, ['', 'gif', 'jpg', 'png']);"/>
		Tags: <input name="upload0tags" type="text" />
		<br />
		<input name="upload1check" type="checkbox" value='true' class="obCheck"/>
		<input name="upload1" id="upload1" type="file" onchange="TestFileType(this.form.upload1.value, ['', 'gif', 'jpg', 'png']);"/>
		Tags: <input name="upload1tags" type="text" />
		<br />
		<input name="upload2check" type="checkbox" value='true' class="obCheck"/>
		<input name="upload2" id="upload2" type="file" onchange="TestFileType(this.form.upload2.value, ['', 'gif', 'jpg', 'png']);"/>
		Tags: <input name="upload2tags" type="text" />
		<br />
		<input name="upload3check" type="checkbox" value='true' class="obCheck"/>
		<input name="upload3" id="upload3" type="file" onchange="TestFileType(this.form.upload3.value, ['', 'gif', 'jpg', 'png']);"/>
		Tags: <input name="upload3tags" type="text" />
		<br />
		<input name="upload4check" type="checkbox" value='true' class="obCheck"/>
		<input name="upload4" id="upload4" type="file" onchange="TestFileType(this.form.upload4.value, ['', 'gif', 'jpg', 'png']);"/>
		Tags: <input name="upload4tags" type="text" />
		<br />
		<input name="upload5check" type="checkbox" value='true' class="obCheck"/>
		<input name="upload5" id="upload5" type="file" onchange="TestFileType(this.form.upload5.value, ['', 'gif', 'jpg', 'png']);"/>
		Tags: <input name="upload5tags" type="text" />
		<br />
		<input name="upload6check" type="checkbox" value='true' class="obCheck"/>
		<input name="upload6" id="upload6" type="file" onchange="TestFileType(this.form.upload6.value, ['', 'gif', 'jpg', 'png']);"/>
		Tags: <input name="upload6tags" type="text" />
		<br />
		<input name="upload7check" type="checkbox" value='true' class="obCheck"/>
		<input name="upload7" id="upload7" type="file" onchange="TestFileType(this.form.upload7.value, ['', 'gif', 'jpg', 'png']);"/>
		Tags: <input name="upload7tags" type="text" />
		<br />
		<input name="upload8check" type="checkbox" value='true' class="obCheck"/>
		<input name="upload8" id="upload8" type="file" onchange="TestFileType(this.form.upload8.value, ['', 'gif', 'jpg', 'png']);"/>
		Tags: <input name="upload8tags" type="text" />
		<br />
		<input name="upload9check" type="checkbox" value='true' class="obCheck"/>
		<input name="upload9" id="upload9" type="file" onchange="TestFileType(this.form.upload9.value, ['', 'gif', 'jpg', 'png']);"/>
		Tags: <input name="upload9tags" type="text" />
		<br />
		<input type="submit" value="Upload File" />
		<br />
		Please view all images before uploading.
	</div>
</form>

<div id="navBar">
	<a href='index.php'>Newest</a>
	<a href='index.php?thumb=true'>Thumbnail view</a>
<?
if($isLoggedIn){
?>
	<a href='logout.php'>Log out <?echo $_COOKIE['username'];?></a>
	<a href='user.php'>Change Settings</a>
<?
}
else{
?>
	<a href='login.php'>Log in</a>
	All images will be directed to the queue.
<?
}

if($isAdmin){
	$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
	$result = mysql_query($sql);
	$result = mysql_fetch_assoc($result);

?>
	<a href='queue.php'>Check the Queue (<?echo $result['COUNT(*)'];?>)</a>
	<a href='adduser.php'>Add a user</a>
<?
}
?>
	<br />
	<a href='index.php?viewObscene=true'>View Obscene</a>
<?
	if($_COOKIE['obscene']){
?>
	You are viewing obscene content.
<?
	}
	else{	
?>
	You are not viewing obscene content.
<?
}
	$sql = "SELECT COUNT(*) FROM images WHERE queue = 0 AND postTime <= " . time();
	$result = mysql_query($sql);
	$result = mysql_fetch_assoc($result);
?>
	<br />
	<a href="bblincoln-latest.tar.gz">Download them all</a> Updates at 1AM PST.
	<br />
	We have <?echo $result['COUNT(*)'];?> images.
	<br />
	<form id='search' enctype="multipart/form-data" action="search.php" method="post">
		<div>
			Tag search:
			<input type='text' name='search' />
			<input type='submit' value='Search' />
		</div>
	</form>
</div>
<div id="mainBody">
	<br />
