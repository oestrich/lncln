<?php
/**
 * info.php
 * 
 * Contains information about the obscene module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Obscene module
 * @since 0.13.0
 * 
 * @return array Information for the Obscene module
 */
function obscene_info(){
	$obscene = array(
		'name' => 'Obsene',
		'class' => 'Obscene',
		'description' => 'Allows you to make images obscene and shrink',
		'version' => '0.13.0',
		'package' => 'optional',
		'requires' => array('index', 'rss'),
	);
	
	return $obscene;
}
