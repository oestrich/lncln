<?php
/**
 * info.php
 * 
 * Info for Captions module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the captions module
 * @since 0.13.0
 */
function captions_info(){
	$captions = array(
		'name' => 'Captions',
		'class' => 'Captions',
		'description' => 'Add captions to pictures',
		'version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $captions;
}