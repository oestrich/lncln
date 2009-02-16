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

require_once("includes/iconActions.php");

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