<?php
/**
 * info.php
 * 
 * Contains information about the image module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Image moudle
 * @since 0.13.0
 * 
 * @return array Information for Image module
 */
function image_info(){
	$image = array(
		'name' => 'Image',
		'class' => 'Image',
		'description' => 'Display only a single image',
		'version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $image;
}