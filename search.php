<?
/*
/	lncln by Eric Oestrich
/	Version .5
/
*/

require_once("config.php");
require_once("functions.php");

connect($config['mysql']);
list($isLoggedIn, $isAdmin, $userID) = loggedIn();

if(!isset($_POST['search']) || $_POST['search'] == ""){
	header("location:index.php");
	exit();
}

list($images, $type, $extra) = img($start, false, $_POST['search']);

require_once("header.php");

?>
	You searched for: <?echo $_POST['search'];?> <br />
<?

$sql = "SELECT MAX(id) FROM images LIMIT 1";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);

$start = $row['MAX(id)'];

require_once('listImages.php');

require_once("footer.php");

?>