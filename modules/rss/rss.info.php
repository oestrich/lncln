<?php
/**
 * rss.info.php
 * 
 * 
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Pulls information from the RSS Module
 * @since 0.13.0
 * 
 * @return array Information from the RSS Module
 */
function rss_info(){
	$info = array(
		'name' => 'RSS',
		'class' => 'RSS',
		'description' => 'Create an RSS feed',
		'version' => '0.13.0',
		'package' => 'core',
		'requires' => array(),
	);
	
	return $info;
}
