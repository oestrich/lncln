<?php
/**
 * rss.admin.php
 * 
 * Admin page for RSS feed
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * RSS Admin panel, lets you change the default loaded RSS page
 * @since 0.13.0
 * 
 * @package lncln
 */
class RSSAdmin extends RSS{
	/**
	 * Actions that User module requires
	 * @since 0.13.0
	 * 
	 * @return array Actions array
	 */
	public function actions(){
		$action = array(
			'urls' => array(
				'Main' => array(
					'settings' => 'Manage settings',
					),
				),
			'quick' => array(
				'settings',
				),
			);
		
		return $action;
	}
	
	/**
	 * Manage which page to load by default
	 * @since 0.13.0
	 */
	public function settings(){
		if($_POST['default_rss_keyword'] != ""){
			$query = array(
				'type' => 'UPDATE',
				'table' => 'settings',
				'set' => array(
					'value' => $_POST['default_rss_keyword'],
					),
				'where' => array(
					array(
						'field' => 'name',
						'compare' => '=',
						'value' => 'default_rss_keyword',
						),
					),
				);
			
			$this->db->query($query);
			
			$this->lncln->display->message("Settings changed.  Click <a href='" . URL . "admin/RSS/settings/'>here</a> to continue.");
		}
		
		$form = array(
			'action' => 'admin/RSS/settings/',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Update settings',
			);
		
		$form['inputs'][] = array(
			'title' => 'Default Keyword',
			'type' => 'select',
			'name' => 'default_rss_keyword',
			'options' => $this->get_keywords(),
			);
		
		create_form($form);
	}
	
	/**
	 * Returns keywords in a select input type for create_form
	 * @since 0.13.0
	 * 
	 * @return array Select array
	 */
	public function get_keywords(){
		$keywords = array(
			array(
				'name' => 'all',
				'value' => 'all',
				),
			);
		
		foreach($this->lncln->modules as $module){
			if(method_exists($module, "rss_keyword")){
				foreach($module->rss_keyword() as $keyword){
					if($this->lncln->display->settings['default_rss_keyword'] == $keyword[0]){
						$selected = array('selected' => true);
					}
					else{
						$selected = array();
					}
					
					$keywords[] = array_merge(
						array(
							'name' => $keyword[0],
							'value' => $keyword[0],
							), 
						$selected);
				}
			}
		}
		
		return $keywords;
	}
}
