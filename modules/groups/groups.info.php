<?php
/**
 * groups.info.php
 * 
 * Information for Groups module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information for the Groups module
 * @since 0.13.0
 * 
 * @return array Information for Groups module
 */
function groups_info(){
	$info = array(
		'name' => 'Groups',
		'class' => 'Groups',
		'description' => 'User permissions',
		'version' => '0.14.0-1',
		'lncln_version' => '0.14.0',
		'package' => 'Core',
		'requires' => array(),
	);
	
	return $info;
}
