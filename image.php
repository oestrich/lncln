<?
/**
 * image.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

$lncln = new lncln("image");
$lncln->loggedIn();

require_once("includes/iconActions.php");

require_once("includes/header.php");

$lncln->img();

if(isset($_GET['img']) && is_numeric($_GET['img'])){
	require_once("includes/listImages.php");	
}
else{
?>
	No such image.
<?
}
require_once("includes/footer.php");
?>