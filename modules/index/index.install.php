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
 * @todo Move news into it's own module
 * @todo Move settings into admin module
 * @since 0.13.0
 * 
 * @return array An array of queries that need to be run
 */
function index_install(){
	/**
	 * @todo Remove parts not required by the index module and seperate them into their own modules
	 */
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
	
	$schema[] = array(
		'type' => 'CREATE TABLE',
		'table' => 'news',
		'fields' => array(
			'id' => array(
				'type' => 'int',
				'size' => 8,
				'null' => false,
				'attributes' => array(
					'auto_increment' => truem
					),
				),
			'postTime' => array(
				'type' => 'int',
				'size' => 32,
				'null' => false,
				),
			'title' => array(
				'type' => 'varchar',
				'size' => 50,
				'null' => false,
				),
			'news' => array(
				'type' => 'text',
				'null' => false,
				),
			),
		'primary key' => array('id'),
		);
	
	$schema[] = array(
		'type' => 'CREATE TABLE',
		'table' => 'settings',
		'fields' => array(
			'id' => array(
				'type' => 'int',
				'size' => 11,
				'null' => false,
				'attributes' => array(
					'auto_increment' => true,
					),
				),
			'name' => array(
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				),
			'value' => array(
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				),
			),
		'primary key' => array('id'),
		);
	
	$schema[] = array(
		'type' => 'INSERT',
		'table' => 'settings',
		'fields' => array('id', 'name', 'value'),
		'values' => array(
			array(1, 'title', 'The Archive'),
			array(2, 'version', '0.12.1'),
			array(3, 'theme', 'bbl'),
			array(4, 'perpage', '3'),
			array(5, 'tbp', '10'),
			array(6, 'default_group', '2'),
			array(7, 'register', '1'),
			array(8, 'default_rss_keyword', 'safe'),
			),
		);
	
	return $schema;
}
