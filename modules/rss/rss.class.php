<?php
/**
 * rss.class.php
 * 
 * Main file for the RSS module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

class RSS extends Module{
	/**
	 * @var string Name of module
	 */
	public $name = "RSS";
		
	/**
	 * @var string Display name of module
	 */
	public $displayName = "RSS";
	
	/**
	 * Outputs the RSS feed
	 * @since 0.13.0
	 */
	public function index(){
		header('Content-type: text/xml'); 
		
		$server = "http://" . $_SERVER['SERVER_NAME'] . str_replace("rss.php", "", $_SERVER['SCRIPT_NAME']);
		
		$this->prepare_rss();
		$this->lncln->get_data();		
		
		echo "<rss version='2.0'>\n";
		echo "<channel>\n";
		
		echo "<title>The Archive</title>\n";
		echo "<link>$server</link>\n";
		echo "<description>All Images</description>\n";
		echo "<lastBuildDate>Mon, 26 Jan 2009 18:37:00 EST</lastBuildDate>\n";
		echo "<language>en-us</language>\n";
		
		foreach($this->lncln->images as $image){
			echo "\t<item>\n";
			echo "\t\t<title>" . $image['id'] . "</title>\n";
			echo "\t\t<link>" . $server . "image/" . $image['id'] . "</link>\n";
			echo "\t\t<guid>" . $server . "image/" . $image['id'] . "</guid>\n";
			echo "\t\t<pubDate>" . date('r', $image['postTime']) . "</pubDate>\n";
			echo "\t\t<description>\n";
			echo "\t\t<![CDATA[ <img src='" . $this->lncln->getImagePath($image['id'], 'index') . "' alt=" . $image['id'] . "'/> <br />\n";
			echo "\t\t\t<a href='" . $this->lncln->getImagePath($image['id'], "full") . "' />Larger</a><br />\n";
			foreach($this->lncln->modules as $module):
				if(method_exists($module, "rss")){
					$output = $module->rss($image['id']);
					if($output != "")
						echo "\t\t\t" . $output . "\n\t\t\t<br/>\n\t\t\t\n";
				}
			endforeach;
			echo "\t\t]]>\n";
			echo "\t\t</description>\n";
			echo "\t</item>\n";
		}
		
		echo "</channel>\n";
		echo "</rss>\n";
		exit();
	}
	
	/**
	 * Gets data ready for the rss feed
	 * @todo move into the RSS module
	 * @since 0.9.0
	 * 
	 * @param array $rss First term is the type of rss feed (all/safe)
	 */
	function prepare_rss(){
		$safe = $this->lncln->params[0] != "all" ? array('field' => 'obscene', 'compare' => '=', 'value' => 0) : array();
		
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 0 " . $safe;
		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('!COUNT(*)'),
			'table' => 'images',
			'where' => array(
				'AND' => array(
					array(
						'field' => 'queue',
						'compare' => '=',
						'value' => 0,
						),
					$safe,
					),
				),
			);
			
		$this->db->query($query);
		$row = $this->db->fetch_one();
		
		if($row['COUNT(*)'] > 0){
			$query = array(
				'type' => 'SELECT',
				'fields' => array('id'),
				'table' => 'images',
				'where' => array(
					'AND' => array(
						array(
							'field' => 'postTime',
							'compare' => '<=',
							'value' => time(),
							),
							$safe,
						),
					),
				'order' => array(
					'DESC',
					array('id'),
					),
				'limit' => array($this->lncln->display->settings['perpage']),
				);
				
			foreach($this->lncln->modules as $module){
				if(method_exists($module, "get_data_sql")){
					$query['where']['AND'][] = $module->get_data_sql();
				}
			}
			
			$this->db->query($query);
			
			foreach($this->db->fetch_all() as $row){
				$this->lncln->imagesToGet[] = $row['id'];
			}
		}
	}
}