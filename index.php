<?
/**
 * index.php
 * 
 * @author Eric Oestrich
 * @version 0.6.0
 * 
 * @package lncln
 */
 

require_once("config.php");
require_once("functions.php");

connect();

//list($isLoggedIn, $isAdmin, $userID) = loggedIn();


$lncln = new lncln();

$lncln->loggedIn();


$isLoggedIn = $lncln->isLoggedIn;
$isAdmin = $lncln->isAdmin;
$userID = $lncln->userID; //ratings won't work for a while

if(isset($_GET['thumb'])){
	$extra = "&thumb=true";
}

if($_GET['post'] == true){
	$lncln->upload();
	header("location:index.php");
	exit();
}

if(isset($_GET['delete']) && $lncln->isAdmin){
	$deletion = $lncln->delete($_GET['delete']);
	header("location:index.php?img=" . $_GET['img'] . $extra);
	exit();
}

if(isset($_GET['obscene']) && $lncln->isLoggedIn){
	$obscene = $lncln->obscene($_GET['obscene']);
	header("location:index.php?img=" . $_GET['img'] . $extra);
	exit();
}

if(isset($_GET['rateUp']) && $lncln->isLoggedIn){
	//This should probably be handled by the function itself
	$rating = 1;
	if($isAdmin){
		$rating = 5;
	}
	$lncln->rate($_GET['rateUp'], $rating);
	header("location:index.php?img=" . $_GET['img'] . $extra);
	exit();
}

if(isset($_GET['rateDown']) && $lncln->isLoggedIn){	
	//This should probably be handled by the function itself
	$rating = -1;
	if($isAdmin){
		$rating = -5;
	}
	$lncln->rate($_GET['rateDown'], $rating);
	header("location:index.php?img=" . $_GET['img'] . $extra);
	exit();
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

if(isset($_GET['refresh']) && $lncln->isLoggedIn){
	$id = stripslashes($_GET['refresh']);
	$id = mysql_real_escape_string($id);
	
	$sql = "SELECT type FROM images WHERE id = " . $id;
	$result = mysql_query($sql);
	
	if(mysql_num_rows($result) == 1){
		$row = mysql_fetch_assoc($result);
		$lncln->thumbnail($id . "." . $row['type']);
	}
	header("location:index.php?img=" . $_GET['img'] . $extra);
	exit();
}

if($_GET['caption'] && $lncln->isLoggedIn){
	$lncln->caption($_POST['id'], $_POST['caption']);
	header("location:index.php?img=" . $_GET['img'] . $extra);
	exit();
}

if($_GET['tag'] && $lncln->isLoggedIn){
	$lncln->tag($_POST['id'], $_POST['tags']);
	header("location:index.php?img=" . $_GET['img'] . $extra);
	exit();
}

//list($start, $prev, $next, $numImgs) = init();
//$start = $lncln->firstImage;
//$prev = $lncln->aboveFifty;
//$next = $lncln->belowFifty;
//$numImgs = $lncln->highestID;

//list($images, $type, $extra) = img($start, false, $isAdmin);
$lncln->img();

//almost time to delete these
//$images = $lncln->images;
//$type = $lncln->type;
//$extra = $lncln->extra;

require_once("header.php");

if($_SESSION['uploaded']){
	for($i = 0; $i < 10; $i++){
		$a = $i + 1;
		switch($_SESSION['upload'][$i]){
			case 0:
				break;
			case 1:
				$date = date('h:i:s A - m/d/Y', $_SESSION['uploadTime'][$i] + (3 * 60 * 60));
				echo "Uploaded #$a correctly. It will appear at $date. To see it now <a href='img/" . $_SESSION['image'][$i] . "'>click here</a>.<br />";
				break;
			case 2:
				echo "Uploaded #$a to the queue. <br />";
				break;
			case 3:
				echo "#$a is missing tags. <br />";
				break;
			case 4:
				echo "#$a is the wrong file type. <br />";
				break;
			case 5:
				echo "#$a got a 404 error. <br />";
				break;
		}
	}
	$_SESSION['pages'] += 1;
	
	if($_SESSION['pages'] >= 1){
		unset($_SESSION['uploaded']);
		unset($_SESSION['upload']);
		unset($_SESSION['uploadTime']);
	}
}
if(isset($deletion)){
	echo $deletion . "<br />";
}
if(isset($obscene)){
	echo $obscene . "<br />";
}

echo $lncln->prevNext();

require_once('listImages.php');

?>
	<div id='bPrevNext'>
<?
echo $lncln->prevNext();
?>
	</div>
<?
require_once("footer.php");
?>