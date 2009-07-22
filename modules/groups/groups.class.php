<?php
/**
 * class.php
 * 
 * Groups module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Groups module main class
 * @since 0.13.0
 * 
 * @package lncln
 */
class Groups extends Module{
	/**
	 * @var string Name of module
	 */
	public $name = "Groups";
	
	/**
	 * @var string Display name for module
	 */
	public $displayName = "Groups";
	
	/**
	 * Print out a list of groups and their members
	 * @since 0.13.0
	 */
	public function index(){
		if($this->lncln->settings['group_listing'] == 1){
			if(isset($this->lncln->params[0]) && is_numeric($this->lncln->params[0])){
				echo "Group members:<br />\n";
				echo "<ul>\n";
				
				foreach($this->get_group_members($this->lncln->params[0]) as $user){
					echo "<li>" . $user['name'] . "</li>\n";
				}
				
				echo "</ul>";
			}
			else{
				foreach($this->get_groups() as $group){
					echo "<a href='" . URL . "groups/" . $group['id']. "'>"; 
					echo $group['name'] . "</a><br />\n";
				}
			}
		}
		else{
			header("location:" . URL . "index/");
		}
	}
	
	/**
	 * Add a permission, boolean only
	 * @since 0.13.0
	 * 
	 * @param string $name
	 * @param bool $admin Default permission for admins
	 * 
	 * @return bool 
	 */
	public function add_permission($name, $admin){
		$query = 'DESCRIBE `groups`';
		$this->db->query($query);
		
		foreach($this->db->fetch_all() as $row){
			if($row['Field'] == $name){
				return false;
			}
		}
		
		$query = array(
			'type' => 'ALTER',
			'table' => 'groups',
			'option' => 'add column',
			'fields' => array(
				$name => array(
					'type' => 'int',
					'size' => 1,
					'null' => false,
					'default' => 0,
					),
				),
			);
		
		$this->db->query($query);
		
		//Update the admin group for their permission
		$query = array(
			'type' => 'UPDATE',
			'table' => 'groups',
			'set' => array(
				$name => $admin,
				),
			'where' => array(
				array(
					'field' => 'name',
					'compare' => '=',
					'value' => 'admin',
					),
				),
			);
		
		$this->db->query($query);
		
		return true;
	}
	
	/**
	 * Get a group's members based on the ID
	 * @since 0.13.0
	 * 
	 * @param int $id Group ID
	 * 
	 * @return array 
	 */
	protected function get_group_members($id){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('id', 'name'),
			'table' => 'users',
			'where' => array(
				array(
					'field' => 'group',
					'compare' => '=',
					'value' => $id,
					),
				),
			);
		
		$this->db->query($query);
		
		return $this->db->fetch_all();
	}
	
	/**
	 * Get all groups
	 * @since 0.12.0
	 * 
	 * @param int $num Number of groups to get
	 * @param int $offset Group to start at
	 * 
	 * @return array Keys: id, name
	 */
	protected function get_groups($num = null, $offset = null){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('id', 'name'),
			'table' => 'groups',
			);
		
		if($num != null){
			$query['limit'] = array($num);
			if($offset != null)
				$query['limit'][] = $offset;
		}
		
		$this->db->query($query);
		
		foreach($this->db->fetch_all() as $row){
			$groups[] = array(
				"id" => $row['id'],
				"name" => $row['name']
				);
		}
		
		return $groups;
	}
}
