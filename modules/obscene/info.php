<?php
/**
 * info.php
 * 
 * Contains information about the obscene module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

function obscene_info(){
	$obscene = array(
		'name' => 'Obsene',
		'class' => 'Obscene',
		'description' => 'Allows you to make images obscene and shrink',
		'version' => '0.13.0',
		'package' => 'optional',
		'requires' => array('index'),
	);
	
	return $obscene;
}