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

include(ABSPATH . "includes/header.php");

echo "Welcome to the admin panel";

foreach($modules as $module){
	include($module . "/info.php");
}

foreach($links as $key => $module){
	echo "<br /><br /><span style='font-weight: bold; text-decoration:underline; font-size:large'>" . $name[$key] . "</span>";
	foreach($module  as $link){
		echo "<a href='" . $modules[$key] . "/index.php?action=" . $link['url'] . "'>" . $link['name'] . "</a><br />";
	}
}

include(ABSPATH . "includes/footer.php");
?>


