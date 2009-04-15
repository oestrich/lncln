<?
/**
 * index.php
 * 
 * Main page, does the "index" action
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

require_once("load.php");

$lncln = new lncln("index");

require_once(ABSPATH . "includes/iconActions.php");

$lncln->img();

require_once(ABSPATH . "includes/header.php");


//News
$sql = "SELECT * FROM `news` ORDER BY id DESC LIMIT 1";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);

?>	<div id="news"> <?
echo $row['news'] . "<br /><br />";
?>	</div> <?

/**
 * Upload block that lets the user know how the upload went
 * Should probably eventually stick this into a function,
 * if only to clean up code
 */
if($_SESSION['uploaded']){
	for($i = 0; $i < 10; $i++){
		$a = $i + 1;
		switch($_SESSION['upload'][$i]){
			case 0:
				break;
			case 1:
				$date = date('h:i:s A - m/d/Y', $_SESSION['uploadTime'][$i] + (3 * 60 * 60));
				echo "Uploaded #$a correctly. It will appear at $date. To see it now <a href='images/full/" . $_SESSION['image'][$i] . "'>click here</a>.<br />";
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
	
	//So it only shows up once
	if($_SESSION['pages'] >= 1){
		unset($_SESSION['uploaded']);
		unset($_SESSION['upload']);
		unset($_SESSION['uploadTime']);
		unset($_SESSION['uploadKey']);
	}
}
if(isset($deletion)){
	echo $deletion . "<br />";
}
if(isset($obscene)){
	echo $obscene . "<br />";
}

echo $lncln->prevNext();

require_once(ABSPATH . "includes/listImages.php");

?>
	<div id='bPrevNext'>
<?
echo $lncln->prevNext();
?>
	</div>
<?
require_once("includes/footer.php");
?>