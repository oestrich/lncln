<?php
/**
 * upgrade.php
 * 
 * Upgrade lncln
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

$GLOBALS['upgrade'] = true;

/** Starts off lncln */
require_once("load.php");

/**
 * Function to upgrade settings table
 * @since 0.13.0
 */
function upgrade_settings(){
	global $db;
	global $lncln;
	
	$query = array(
		'type' => 'SELECT',
		'fields' => array('name', 'value'),
		'table' => 'settings',
		);
	
	$db->query($query);
	
	foreach($db->fetch_all() as $row){
		if(!unserialize($row['value'])){
			if(is_numeric($row['value'])){
				$row['value'] = (int) $row['value'];
			}
			
			$lncln->setting_set($row['name'], $row['value']);
		}
	}
}

/**
 * Function to load enabled modules into the database
 * @since 0.14.0
 */
function upgrade_modules_install(){
	global $modules_enabled;
	
	// Include the files since its not included during an upgrade
	foreach($modules_enabled as $folder => $class){
		/** Include the main class file for modules */
		include_once(ABSPATH . "modules/" . $folder . "/" . $folder . ".class.php");
		/** Include the info file for modules */
		include_once(ABSPATH . "modules/" . $folder . "/" . $folder . ".info.php");
	}
	
	$db = get_db();
	
	$query = array(
		'type' => 'CREATE TABLE',
		'table' => 'modules',
		'fields' => array(
			'id' => array(
				'type' => 'int',
				'size' => 8,
				'null' => false,
				'attributes' => array(
					'unsigned' => true,
					'auto_increment' => true,
					),
				),
			'name' => array(
				'type' => 'varchar',
				'size' => 32,
				'null' => false,
				),
			'description' => array(
				'type' => 'varchar',
				'size' => 256,
				'null' => false,
				),
			'class' => array(
				'type' => 'varchar',
				'size' => 32,
				'null' => false,
				),
			'folder' => array(
				'type' => 'varchar',
				'size' => 32,
				'null' => false,
				),
			'enabled' => array(
				'type' => 'tinyint',
				'size' => 1,
				'null' => false,
				'default' => 0,
				),
			'package' => array(
				'type' => 'varchar',
				'size' => 32,
				'null' => false,
				),
			'version' => array(
				'type' => 'varchar',
				'size' => 32,
				'null' => false,
				),
			'lncln_version' => array(
				'type' => 'varchar',
				'size' => 32,
				'null' => false,
				),
			'requires' => array(
				'type' => 'text',
				'null' => false,
				),
			),
		'primary key' => array('id'),
		);
	
	$db->query($query);
	
	ksort($modules_enabled);
	
	foreach($modules_enabled as $folder => $module){
		$mod_info = $module . "_info";
		$mod_info = $mod_info();
		
		$query = array(
			'type' => 'INSERT',
			'table' => 'modules',
			'fields' => array(
				'name',
				'description',
				'class',
				'folder',
				'enabled',
				'package',
				'version',
				'lncln_version',
				'requires',
				),
			'values' => array(
				array(
					$mod_info['name'],
					$mod_info['description'],
					$mod_info['class'],
					$folder,
					1,
					$mod_info['package'],
					$mod_info['version'],
					$mod_info['lncln_version'],
					serialize($mod_info['requires']),
					),
				),
			);
		
		$db->query($query);
	}
}

if($lncln->user->permissions['isAdmin'] == true){
	upgrade_modules_install();
}
