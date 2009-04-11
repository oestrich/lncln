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

 
/*
 * This part will be removed once I get it configurable in the database
 * It will say which modules are enabled 
 */
/**
 * The enabled modules
 * 
 * @since 0.11.0
 * @package lncln
 */
$modules = array("users" => "users", "albums" => "albums", "settings" => "settings");
 

/**
 * Creates URLs that are ready to be used in links.
 * 
 * @since 0.11.0
 * @package lncln
 * 
 * @param $action string The action to be used
 * @param $params array Key and value will become "&key=value" in link
 * 
 * @return string The link that was created
 */
function createLink($action, $params = array()){
	$link = "index.php?action=" . $action;
	
	foreach($params as $key => $value){
		$link .= "&amp;" . $key . "=" . $value;
	}
	
	return $link;
}

?>