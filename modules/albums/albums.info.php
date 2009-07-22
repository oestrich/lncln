<?php
/**
 * info.php
 * 
 * Contains information about the Album module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Albums module
 * @since 0.13.0
 * 
 * @return array Information for Albums
 */
function albums_info(){
	$albums = array(
		'name' => 'Albums',
		'class' => 'Albums',
		'description' => 'Create albums to seperate pictures',
		'version' => '0.13.0',
		'lncln_version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $albums;
}
