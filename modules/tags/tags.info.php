<?php
/**
 * info.php
 * 
 * Info for Tags module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Tags module
 * @since 0.13.0
 * 
 * @return Information for the Tags module
 */
function tags_info(){
	$tags = array(
		'name' => 'Tags',
		'class' => 'Tags',
		'description' => 'Add tags to images',
		'version' => '0.13.0',
		'lncln_version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $tags;
}
