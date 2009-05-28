<?php
/**
 * info.php
 * 
 * Info for Report module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Report module
 * @since 0.13.0
 */
function report_info(){
	$info = array(
		'name' => 'Report',
		'class' => 'Report',
		'description' => 'Report bad images and stick them back in the queue',
		'version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $info;
}
