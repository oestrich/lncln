<?php
/**
 * info.php
 * 
 * Info for the Admin module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

function admin_info(){
	$admin = array(
		'name' => 'Admin',
		'class' => 'Admin',
		'description' => 'The admin panel',
		'version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $admin;
}