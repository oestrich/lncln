<?php
/**
 * info.php
 * 
 * Info for the Admin module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0-1 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Admin module
 * @since 0.13.0
 * 
 * @return array
 */
function admin_info(){
	$admin = array(
		'name' => 'Admin',
		'class' => 'Admin',
		'description' => 'The admin panel',
		'version' => '0.14.0-1',
		'lncln_version' => '0.14.0',
		'package' => 'Core',
		'requires' => array(),
	);
	
	return $admin;
}
