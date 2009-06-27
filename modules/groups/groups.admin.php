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
	 * Edit a group
	 * @since 0.13.0
	 * 
	 * @uses edit_group()
	 */
	public function edit(){
		if(isset($_POST['name'])){
			$this->lncln->display->message($this->edit_group($this->lncln->params[2], $_POST) . 
				" Click <a href='" . URL . "admin/Groups/manage'>here</a> to continue managing groups.");
		}
		
		$group = $this->get_group($this->lncln->params[2]);

		$form = array(
			'action' => 'admin/Groups/edit/' . $group['id'],
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Submit',
			);
		
		$form['inputs'][] = array(
			'title' => 'Name',
			'name' => 'name',
			'type' => 'text',
			'value' => $group['name'],
			);
		
		$form['inputs'][] = array(
			'title' => 'Upload',
			'name' => 'upload',
			'type' => 'select',
			'options' => $this->select_options($group['upload']),
			);

		$form['inputs'][] = array(
			'title' => 'Directly to the index',
			'name' => 'index',
			'type' => 'select',
			'options' => $this->select_options($group['index']),
			);
		
		$form['inputs'][] = array(
			'title' => 'Report',
			'name' => 'report',
			'type' => 'select',
			'options' => $this->select_options($group['report']),
			);
		
		$form['inputs'][] = array(
			'title' => 'Report value',
			'name' => 'reportValue',
			'type' => 'text',
			'value' => $group['reportValue'],
			);
		
		$form['inputs'][] = array(
			'title' => 'Rate',
			'name' => 'ratings',
			'type' => 'select',
			'options' => $this->select_options($group['ratings']),
			);
		
		$form['inputs'][] = array(
			'title' => 'Rate value',
			'name' => 'ratingsValue',
			'type' => 'text',
			'value' => $group['ratingsValue'],
			);
		
		$form['inputs'][] = array(
			'title' => 'Use obscene',
			'name' => 'obscene',
			'type' => 'select',
			'options' => $this->select_options($group['obscene']),
			);
		
		$form['inputs'][] = array(
			'title' => 'Use refresh',
			'name' => 'refresh',
			'type' => 'select',
			'options' => $this->select_options($group['refresh']),
			);
		
		$form['inputs'][] = array(
			'title' => 'Use delete',
			'name' => 'delete',
			'type' => 'select',
			'options' => $this->select_options($group['delete']),
			);
		
		$form['inputs'][] = array(
			'title' => 'Use captions',
			'name' => 'captions',
			'type' => 'select',
			'options' => $this->select_options($group['captions']),
			);
		
		$form['inputs'][] = array(
			'title' => 'Use tags',
			'name' => 'tags',
			'type' => 'select',
			'options' => $this->select_options($group['tags']),
			);
		
		$form['inputs'][] = array(
			'title' => 'Manage albums',
			'name' => 'albums',
			'type' => 'select',
			'options' => $this->select_options($group['albums']),
			);
		
		create_form($form);
	}

	/**
	 * Manage groups, links to edit and delete
	 * @since 0.13.0
	 * 
	 * @uses get_groups()
	 * @uses delete_group()
	 */
	public function manage(){
		if($this->lncln->params[2] == "delete"){
			if(is_numeric($_POST['group'])){
				$this->lncln->display->message($this->delete_group($this->lncln->params[3], $_POST['group']));
			}
			
			$group = $this->get_group($_GET['group']);
	
			echo "Move all users from " . $group['name'] . " to which group?<br />";

			$form = array(
				'action' => 'admin/Groups/manage/delete/' . $this->lncln->params[3],
				'method' => 'post',
				'inputs' => array(),
				'file' => false,
				'submit' => 'Submit',
				);

			$form['inputs'][] = array(
				'title' => 'Group',
				'name' => 'group',
				'type' => 'select',
				'options' => array(),
				);
				
			foreach($this->get_groups() as $group){
				if($group['id'] == $this->lncln->params[3])
					continue;
				
				$form['inputs'][0]['options'][] = array(
					'name' => $group['name'],
					'value' => $group['id'],
					);
			}
			
			create_form($form);
			
			return "";
		}
		
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
	 * Deletes a group
	 * @since 0.12.0
	 *  
	 * @param $id int Group to be deleted
	 * @param $moveTo int Group users will be moved to
	 * 
	 * @return string Message
	 */
	protected function delete_group($id, $moveTo){
		if(is_numeric($id) && is_numeric($moveTo)){
			$group = $this->get_group($id);
			
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
	 * Create the options section of the form for yes or no permissions
	 * @since 0.13.0
	 * 
	 * @param int $selected 0 for No, 1 for Yes
	 * 
	 * @return array Option section of form input array
	 */
	protected function select_options($selected){
		$options = array(
			array(
				'name' => 'No',
				'value' => 0,
				'selected' => $selected == 0 ? true : false,
				),
			array(
				'name' => 'Yes',
				'value' => 1,
				'selected' => $selected == 1 ? true : false,
				),
			);
		
		return $options;
	}
}
