<?php
/**
 * upload.info.php
 * 
 * Information regarding the Upload module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Upload module
 * @since 0.13.0
 * 
 * @return array Information for the Upload module
 */
function upload_info(){
	$info = array(
		'name' => 'Upload',
		'class' => 'Upload',
		'description' => 'Upload images to lncln',
		'version' => '0.14.0-1',
		'lncln_version' => '0.14.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $info;
}
