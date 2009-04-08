<?
/**
 * admin.php
 * 
 * An important file in the admin panel
 * Checks and makes sure that the user is an admin before continuing.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

if($lncln->user->permissions['isAdmin'] == 0){
	require_once("../includes/header.php");
	echo "You must be an admin to be here";
	require_once(ABSPATH . "includes/footer.php");
	exit();
}
 
?>