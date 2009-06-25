<?php
/**
 * ratings.install.php
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
function ratings_install(){
	$schema[] = array(
		'type' => 'CREATE TABLE',
		'table' => 'rating',
		'fields' => array(
			'id' => array(
				'type' => 'int',
				'size' => 8,
				'null' => false,
				'attributes' => array(
					'auto_increment' => true,
					),
				),
			'picId' => array(
				'type' => 'int',
				'size' => 6,
				'null' => false,
				'attributes' => array(
					'unsigned' => true,
					'zerofill' => true,
					),
				),
			'userId' => array(
				'type' => 'int',
				'size' => 8,
				'null' => false,
				),
			'rating' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			),
		'primary key' => array('id'),
		);
	
	return $schema;
}
