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
require_once("includes/functions.php");

connect();

$lncln = new lncln();
$lncln->loggedIn();

require_once(ABSPATH . "includes/iconActions.php");

$lncln->img();

require_once("includes/header.php");

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

require_once("includes/listImages.php");

?>
	<div id='bPrevNext'>
<?
echo $lncln->prevNext();
?>
	</div>
<?
require_once("includes/footer.php");
?>