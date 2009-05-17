<?php
/**
 * info.php
 * 
 * Contains information about the image module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

function image_info(){
	$image = array(
		'name' => 'Image',
		'description' => 'Display only a single image',
		'version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $image;
}
