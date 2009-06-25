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

function obscene_install(){
	$schema = array(
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
