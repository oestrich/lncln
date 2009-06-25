<?php
/**
 * groups.install.php
 * 
 * Install File
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Called when module is first enabled.
 * @todo Remove other module's permissions
 * @since 0.13.0
 * 
 * @return array An array of queries that need to be run
 */
function groups_install(){
	$schema[] = array(
		'type' => 'CREATE TABLE',
		'table' => 'groups',
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
				'size' => 30,
				'null' => false,
				),
			'upload' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			'index' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			'numIndex' => array(
				'type' => 'int',
				'size' => 3,
				'null' => false,
				),
			'report' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			'reportValue' => array(
				'type' => 'int',
				'size' => 2,
				'null' => false,
				'default' => 0,
				),
			'ratings' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			'ratingsValue' => array(
				'type' => 'int',
				'size' => 2,
				'null' => false,
				'default' => 0,
				),
			'obscene' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			'refresh' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			'delete' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			'captions' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			'tags' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			'albums' => array(
				'type' => 'int',
				'size' => 1,
				'null' => false,
				),
			),
		'primary key' => array('id'),
		);
	
	$schema[] = array(
		'type' => 'INSERT',
		'table' => 'groups',
		'fields' => array(
			'id',
			'name',
			'upload',
			'index',
			'numIndex',
			'report',
			'reportValue',
			'ratings',
			'ratingsValue',
			'obscene',
			'refresh',
			'delete',
			'captions',
			'tags',
			'albums',
			),
		'values' => array(
			array(1, 'Anonymous', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
			array(2, 'New User', 1, 1, 5, 1, 3, 1, 1, 0, 0, 0, 0, 1, 0),
			array(3, 'Trusted User', 1, 1, 20, 1, 5, 1, 3, 1, 0, 0, 1, 1, 1),
			array(4, 'Admin', 1, 1, 0, 1, 5, 1, 5, 1, 1, 1, 1, 1, 1),
			),
		);
	
	return $schema;
}
