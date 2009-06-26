<?php
/**
 * upgrade.php
 * 
 * Upgrade lncln
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

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

upgrade_settings();
