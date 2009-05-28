<?php
/**
 * info.php
 * 
 * Information for the User module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the User module
 * @since 0.13.0
 */
function user_info(){
	$info = array(
		'name' => 'User',
		'class' => 'User',
		'description' => 'Logging in/out, change settings',
		'version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $info;
}