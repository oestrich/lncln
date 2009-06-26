<?php
/**
 * groups.admin.php
 * 
 * Groups admin class
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Groups admin class
 * @since 0.13.0
 * 
 * @package lncln
 */
class GroupsAdmin extends Groups{
	/**
	 * Registers actions that will be used in the admin panel
	 * @since 0.13.0
	 * 
	 * @return array Keys: url 
	 */
	public function actions(){
		$action = array(
			'urls' => array(
				'Main' => array(
					'add' => 'Add group',
					'manage' => 'Manage groups',
					'edit' => '',
					),
				),
			'quick' => array(
				'add', 'manage',
				),
			);
		
		return $action;
	}
	
	/**
	 * Manage groups, links to edit and delete
	 * @since 0.13.0
	 */
	public function manage(){
		echo "Groups:<br />\n";
		echo "<ul>\n";

		foreach($this->get_groups() as $group){		
			echo "\t<li>" . $group['name'] . " <a href='" . URL . "admin/Groups/edit/" . $group['id'] . "'>Edit</a> ";
			echo "<a href='" . URL . "admin/Groups/manage/delete/" . $group['id'] . "'>Delete</a></li>\n";
		}	
	
		echo "</ul>";
	}
	
	/**
	 * Add a new group
	 * @todo Remove other module's permissions
	 * @since 0.12.0
	 * 
	 * @param $data array Info needed for a new group
	 * 
	 * @return string Message
	 */
	protected function add_group($data){
		$query = array(
			'type' => 'INSERT',
			'table' => 'groups',
			'fields' => array(),
			'values' => array(
				array(),
				),
			);
		
		foreach($data as $key => $value){
			$query['fields'][] = $key;
			$query['values'][0][] = $value;
		}
		
		$this->db->query($query);
		
		return "Group " . $data['name'] . " added.";
	}
	
	/**
	 * Edits a group
	 * @since 0.12.0
	 * 
	 * @param $id int Group ID
	 * @param $data array Info needed for group
	 * 
	 * @return string Message
	 */
	protected function edit_group($id, $data){		
		$query = array(
			'type' => 'UPDATE',
			'table' => 'groups',
			'set' => array(),
			'where' => array(
				array(
					'field' => 'id',
					'compare' => '=',
					'value' => $id,
					),
				),
			);
		
		foreach($data as $key => $value){
			$query['set'][$key] = $value;
		}
		
		$this->db->query($query);
		
		return "Group " . $data['name'] . " edited.";
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
	
	/**
	 * Return all of the permissions in a group
	 * @since 0.12.0
	 * 
	 * @param $id int Group id
	 * 
	 * @return array Contains the groups permissions
	 */
	protected function get_group($id){
		if(is_numeric($id)){
			$query = array(
				'type' => 'SELECT',
				'fields' => array('*'),
				'table' => 'groups',
				'where' => array(
					array(
						'field' => 'id',
						'compare' => '=',
						'value' => $id,
						),
					),
				'limit' => array(1),
				);
			
			$this->db->query($query);
			$row = $this->db->fetch_one();
			
			return $row;
		}
		return array();
	}
	
	/**
	 * Deletes a group
	 * @since 0.12.0
	 *  
	 * @param $id int Group to be deleted
	 * @param $moveTo int Group users will be moved to
	 * 
	 * @return string Message
	 */
	protected function deleteGroup($id, $moveTo){
		if(is_numeric($id) && is_numeric($moveTo)){
			$group = $this->getGroup($id);
			
			$query = array(
				'type' => 'UPDATE',
				'table' => 'users',
				'set' => array(
					'group' => $moveTo,
					),
				'where' => array(
					array(
						'field' => 'group',
						'compare' => '=',
						'value' => $id,
						),
					),
				);
			
			$this->db->query($query);
			
			$sql = "DELETE FROM groups WHERE id = " . $id;
			$this->db->query($sql);
			
			return "Deleted " . $group['name'] . ".  Click <a href='" . URL . 
					"admin/Groups/manage'>here</a> to continue managing groups.";
		}
		return "";
	}
}
