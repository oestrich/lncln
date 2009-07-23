<?php
/**
 * info.php
 * 
 * Info for Report module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Report module
 * @since 0.13.0
 * 
 * @return array Information for the Report module
 */
function report_info(){
	$info = array(
		'name' => 'Report',
		'class' => 'Report',
		'description' => 'Report bad images and stick them back in the queue',
		'version' => '0.14.0-1',
		'lncln_version' => '0.14.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $info;
}
