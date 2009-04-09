<?
/**
 * index.php
 * 
 * Main administration panel for lncln
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("../load.php");

$lncln = new lncln();

include("admin.php");

/*Links that will be needed for here later.
		<a href='<?echo URL;?>admin/adduser.php'>Add a user</a>
		<a href='<?echo URL;?>admin/albums.php'>Edit Albums</a>
 */
 
/*
 * This part will be removed once I get it configurable in the database
 * It will say which modules are enabled 
 */
$enabledModules = array("users");

include(ABSPATH . "includes/header.php");

echo "Welcome to the admin panel";
include($enabledModules[0] . "/info.php");

foreach($links as $module){
	foreach($module  as $link){
		echo "<a href='" . $link['url'] . "'> " . $link['name'] . "</a><br />";
	}
}

include(ABSPATH . "includes/footer.php");
?>


