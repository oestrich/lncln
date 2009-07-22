<?php
/**
 * user.install.php
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
function user_install(){
	$schema[] = array(
		'type' => 'CREATE TABLE',
		'table' => 'users',
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
				'type' => 'char',
				'size' => 32,
				'null' => false,
				),
			'password' => array(
				'type' => 'char',
				'size' => 40,
				'null' => false,
				),
			'admin' => array(
				'type' => 'tinyint',
				'size' => 1,
				'null' => false,
				'default' => 0,
				),
			'group' => array(
				'type' => 'int',
				'size' => 3,
				'null' => false,
				),
			'obscene' => array(
				'type' => 'tinyint',
				'size' => 1,
				'null' => false,
				'default' => 1,
				),
			'numImages' => array(
				'type' => 'int',
				'size' => 2,
				'null' => false,
				'default' => 0,
				),
			'postTime' => array(
				'type' => 'int',
				'size' => 32,
				'null' => false,
				'default' => 0,
				),
			'uploadCount' => array(
				'type' => 'int',
				'size' => 8,
				'null' => false,
				'default' => 0,
				),
			),
		'primary key' => array('id'),
		);
	
	$schema[] = array(
		'type' => 'INSERT',
		'table' => 'users',
		'fields' => array(
			'name',
			'password',
			'admin',
			'group',
			'obscene',
			'numImages',
			'uploadCount',
			),
		'values' => array(
			array('Anonymous', '', 0, 1, 0, 1, 1245939661, 8),
			array('admin', 'fa0af50b8e6656579f92f36f997e60d9bdc1e4d2', 1, 4, 0, 1, 1245735445, 18),
			),
		);
	
	return $schema;
}