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
 
include(ABSPATH . "includes/header.php");

echo "Welcome to the admin panel";


include(ABSPATH . "includes/footer.php");
?>


