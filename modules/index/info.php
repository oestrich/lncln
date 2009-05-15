<?php
/**
 * info.php
 * 
 * Info for Index module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

function index_info(){
	$index = array(
		'name' => 'Index',
		'description' => 'Your front page',
		'version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $index;
}
