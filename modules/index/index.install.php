<?php
/**
 * index.install.php
 * 
 * Install file for the Index module
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
function index_install(){
	$schema[] = array(
		'type' => 'CREATE TABLE',
		'table' => 'images',
		'fields' => array(
			'id' => array(
				'type' => 'int',
				'size' => 6,
				'null' => false,
				'attributes' => array(
					'unsigned' => true,
					'zerofill' => true,
					'auto_increment' => true,
					),
				),
			'name' => array(
				'type' => 'char',
				'size' => 40,
				'null' => false,
				),
			'caption' => array(
				'type' => 'text',
				'null' => false,
				),
			'postTime' => array(
				'type' => 'int',
				'size' => 12,
				'null' => false,
				'default' => 0,
				),
			'type' => array(
				'type' => 'enum',
				'size' => 3,
				'options' => array('jpg', 'png', 'gif'),
				'null' => false,
				),
			'album' => array(
				'type' => 'int',
				'size' => 8,
				'null' => false,
				'default' => 0,
				),
			'queue' => array(
				'type' => 'tinyint',
				'size' => 1,
				'null' => false,
				'default' => 1,
				),
			'rating' => array(
				'type' => 'int',
				'size' => 4,
				'null' => false,
				'default' => 0,
				),
			'numComments' => array(
				'type' => 'int',
				'size' => 4,
				'null' => false,
				'default' => 0,
				),
			'obscene' => array(
				'type' => 'tinyint',
				'size' => 1,
				'null' => false,
				'default' => 0,
				),
			'report' => array(
				'type' => 'int',
				'size' => 2,
				'null' => false,
				'default' => 0,
				),
			),
		'primary key' => array('id'),
		);
	
	return $schema;
}