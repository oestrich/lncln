<?
/**
 * image.php
 * 
 * Page to display only one image, the image id in $_GET['img']
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("load.php");

$lncln->image();

include_once(ABSPATH . "includes/iconActions.php");

include_once(ABSPATH . "includes/header.php");

$lncln->img();

if(isset($_GET['img']) && is_numeric($_GET['img'])){
	include_once(ABSPATH . "includes/listImages.php");	
}
else{
	echo "No such image.";
}
include_once(ABSPATH . "includes/footer.php");
?>