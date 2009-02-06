<?
/*
/	lncln by Eric Oestrich
/	Version .5
/
*/

require_once('config.php');
require_once('functions.php');

connect($config['mysql']);

if(isset($_COOKIE['username'])){
	setcookie("username", "", time() - (60 * 60 * 24));
	setcookie("password", "", time() - (60 * 60 * 24));
}


require_once('header.php');

if(isset($_COOKIE['username'])){
?>
	You are now logged out.
<?
}
else{
?>
	Please log in before you can log out.
<?}

require_once('footer.php');
?>