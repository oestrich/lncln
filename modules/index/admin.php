<?php
/**
 * admin.php
 * 
 * 
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

class IndexAdmin extends Index{
	public function __construct(&$lncln){
		parent::__construct($lncln);
	}
	
	public function actions(){
		$actions = array(
			'urls' => array(
				'manage' => 'Manage settings',
				),
			);
		
		return $actions;
	}
	
	public function manage(){
		$form = array(
			'action' => 'admin/Index/manage',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Change Settings',
			);
		
		$form['inputs'][] = array(
			'title' => 'Title',
			'type' => 'text',
			'name' => 'title',
			);
		
		$form['inputs'][] = array(
			'title' => 'Images/Page',
			'type' => 'text',
			'name' => 'perpage',
			);
		
		$form['inputs'][] = array(
			'title' => 'Time between posts',
			'type' => 'text',
			'name' => 'tbp',
			);
		
		$form['inputs'][] = array(
			'title' => 'Theme',
			'type' => 'select',
			'options' => array(
				array(
					'name' => 'bbl',
					'value' => 'bbl',
					'selected' => true,
					),
				),
			'name' => 'theme',
			);
		
		echo create_form($form);
	}
}