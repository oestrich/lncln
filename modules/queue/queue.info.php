<?php
/**
 * info.php
 * 
 * Info file for the Queue module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Queue module
 * @since 0.13.0
 * 
 * @return array Information for the Queue module
 */
function queue_info(){
	$info = array(
		'name' => 'Queue',
		'class' => 'Queue',
		'description' => 'Creates a queue for new uploaded images',
		'version' => '0.14.0-1',
		'lncln_version' => '0.14.0',
		'package' => 'Image',
		'requires' => array('Image', 'Admin'),
	);
	
	return $info;
}
