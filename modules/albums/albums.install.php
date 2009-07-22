<?php
/**
 * albums.install.php
 * 
 * Install file
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
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
function albums_install(){
	$schema[] = array(
		'type' => 'CREATE TABLE',
		'table' => 'albums',
		'fields' => array(
			'id' => array(
				'type' => 'int',
				'size' => 8,
				'null' => false,
				'attributes' => array(
					'auto_increment' => true,
					),
				),
			'name' => array(
				'type' => 'varchar',
				'size' => 50,
				'null' => false,
				),
			),
		'primary key' => array('id'),
		);
	
	return $schema;
}
