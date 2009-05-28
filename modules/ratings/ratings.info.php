<?php
/**
 * info.php
 * 
 * Info for Ratings module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

function ratings_info(){
	$ratings = array(
		'name' => 'Ratings',
		'class' => 'Ratings',
		'description' => 'Have images be rated by your users',
		'version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $ratings;
}
