<?php
/**
 * info.php
 * 
 * Info for Captions module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Captions module
 * @since 0.13.0
 * 
 * @return array Information for Captions module
 */
function captions_info(){
	$info = array(
		'name' => 'Captions',
		'class' => 'Captions',
		'description' => 'Add captions to pictures',
		'version' => '0.14.0-1',
		'lncln_version' => '0.14.0',
		'package' => 'Image',
		'requires' => array('Image'),
	);
	
	return $info;
}
