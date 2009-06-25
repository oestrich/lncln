<?php
/**
 * obscene.install.php
 * 
 * Install file
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Called when module is first enabled.
 * @since 0.13.0
 * 
 * @return array An array of queries that need to be run
 */
function obscene_install(){
	$schema[] = array(
		'type' => 'CREATE TABLE',
		'table' => 'images',
		'fields' => array(
			'obscene' => array(
				'type' => 'tinyint',
				'size' => 1,
				'null' => false,
				'default' => 0,
				),
			),
		);
	
	return $schema;
}
