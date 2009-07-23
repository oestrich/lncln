<?php
/**
 * info.php
 * 
 * Info for Index module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Index module
 * @since 0.13.0
 * 
 * @return array Information for Index module
 */
function index_info(){
	$info = array(
		'name' => 'Settings',
		'class' => 'Index',
		'description' => 'Manage settings for lncln',
		'version' => '0.14.0-1',
		'lncln_version' => '0.14.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $info;
}
