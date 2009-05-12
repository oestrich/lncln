<?
/**
 * image.php
 * 
 * Page to display only one image, the image id in $_GET['img']
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

require_once("load.php");

$lncln->image();

include_once(ABSPATH . "includes/iconActions.php");

include_once(ABSPATH . "includes/header.php");

$lncln->img();

if(isset($_GET['image']) && is_numeric($_GET['image'])){
	include_once(ABSPATH . "includes/listing.php");	
}
else{
	echo "No such image.";
}
include_once(ABSPATH . "includes/footer.php");
?>