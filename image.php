<?
/**
 * image.php
 * 
 * @author Eric Oestrich
 * @version 0.6.0
 * 
 * @package lncln
 */

require_once("config.php");
require_once("functions.php");

connect();

$lncln = new lncln();
$lncln->loggedIn();


if(isset($_GET['obscene']) && $isLoggedIn){
	$obscene = obscene($_GET['obscene']);
	$image = $_GET['obscene'];
}

if(isset($_GET['rateUp']) && $isLoggedIn){
	$rating = 1;
	if($isAdmin){
		$rating = 5;
	}
	rate($_GET['rateUp'], $userID, $rating);
	$image = $_GET['rateUp'];
}

if(isset($_GET['rateDown']) && $isLoggedIn){
	$rating = -1;
	if($isAdmin){
		$rating = -5;
	}
	rate($_GET['rateDown'], $userID, $rating);
	$image = $_GET['rateDown'];
}

if($_GET['viewObscene']){
	if($_COOKIE['obscene'] == false || !$_COOKIE['obscene']){
		setcookie('obscene', true, time() + (60 * 60 * 24));
	}
	else{
		setcookie('obscene', true, time() - (60 * 60 * 24));
	}
	header("location:index.php");	
	exit();
}

if(isset($_GET['refresh']) && $isLoggedIn){
	$id = stripslashes($_GET['refresh']);
	$id = mysql_real_escape_string($id);
	
	$sql = "SELECT type FROM images WHERE id = " . $id;
	$result = mysql_query($sql);
	
	if(mysql_num_rows($result) == 1){
		$row = mysql_fetch_assoc($result);
		thumbnail($id . "." . $row['type'], $curURL);
	}
	header("location:image.php?img=" . $id);
	exit();
}

if($_GET['caption'] && $isLoggedIn){
	caption($_POST['id'], $_POST['caption']);
	header("location:image.php?img=" . $_GET['img']);
	exit();
}

if($_GET['tag'] && $isLoggedIn){
	tag($_POST['id'], $_POST['tags']);
	header("location:image.php?img=" . $_GET['img']);
	exit();
}

require_once("header.php");

if(isset($_GET['img']) || isset($image)){
	if(isset($_GET['img'])){
		$image = stripslashes($_GET['img']);
	}
	if(isset($image)){
		$image = stripslashes($image);
	}
	$image = mysql_real_escape_string($image);
	
	$sql = "SELECT tag FROM tags WHERE picId = " . $image;
	$tags = mysql_query($sql);
	
	$imageTags = array();
	
	while($tag = mysql_fetch_assoc($tags)){
		$imageTags[] = $tag['tag'];
	}	
	
	$sql = "SELECT * FROM images WHERE id = " . $image . " LIMIT 1";
	$result = mysql_query($sql);

	if(mysql_num_rows($result) == 1){
		$image = mysql_fetch_assoc($result);
		
		$images[0] = array(
			'id' 		=> $image['id'],
			'file' 		=> $image['id'] . "." . $image['type'],
			'type'		=> $image['type'],
			'obscene' 	=> $image['obscene'],
			'rating' 	=> $image['rating'],
			'postTime'	=> $image['postTime'],
			'caption'	=> $image['caption'],
			'tags' 		=> $imageTags
		);
		
		$type = 'normal';
		$start = $image['id'];
		
		require_once('listImages.php');	
	}
}
else{
?>
	No such image.
<?
}
require_once("footer.php");
?>