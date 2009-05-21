<?
/**
 * class.php
 * 
 * Main class for the group module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */ 
 
class GroupAdmin extends lncln{

	/**
	 * Add a new group
	 * @since 0.12.0
	 * 
	 * @param $data array Info needed for a new group
	 * 
	 * @return string Message
	 */
	function addGroup($data){
		foreach($data as $key => $value){
			$data[$key] = $this->db->prep_sql($value);
		}
		
		$name = $data['name'];
		$upload = $data['upload'];
		$index = $data['index'];
		$numIndex = $data['numIndex'];
		$report = $data['report'];
		$reportValue = $data['reportValue'];
		$ratings = $data['ratings'];
		$ratingsValue = $data['ratingsValue'];
		$obscene = $data['obscene'];
		$refresh = $data['refresh'];
		$delete = $data['delete'];
		$captions = $data['captions'];
		$tags = $data['tags'];
		$albums = $data['albums'];
		
		if(!is_numeric($upload) || !is_numeric($numIndex) || !is_numeric($report) || !is_numeric($ratings)){
			return "Invalid field";
		}
		
		$sql = 	"INSERT INTO groups (`name`, `upload`, `index`, `numIndex`, `report`, `reportValue`, `ratings`, `ratingsValue`, `obscene`, `refresh`, `delete`, `captions`, `tags`, `albums`) " .
				"VALUES ('$name', $upload, $index, $numIndex, $report, $reportValue, $ratings, $ratingsValue, $obscene, $refresh, $delete, $captions, $tags, $albums)";
		
		mysql_query($sql);
		
		return "Group " . $name . " added.";
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
	function editGroup($id, $data){
		$data = array_map("$this->db->prep_sql", $data);
		
		$name = $data['name'];
		$upload = $data['upload'];
		$index = $data['index'];
		$numIndex = $data['numIndex'];
		$report = $data['report'];
		$reportValue = $data['reportValue'];
		$ratings = $data['ratings'];
		$ratingsValue = $data['ratingsValue'];
		$obscene = $data['obscene'];
		$refresh = $data['refresh'];
		$delete = $data['delete'];
		$captions = $data['captions'];
		$tags = $data['tags'];
		$albums = $data['albums'];
		
		if(!is_numeric($upload) || !is_numeric($numIndex) || !is_numeric($report) || !is_numeric($ratings)){
			return "Invalid field";
		}
		
		$sql = "UPDATE groups SET `name` = '$name', `upload` = $upload, `index` = $index, `numIndex` = $numIndex, `report` = $report, `reportValue` = $reportValue, " .
			   "`ratings` = $ratings, `ratingsValue` = $ratingsValue, `obscene` = $obscene, `refresh` = $refresh, `delete` = $delete, `captions` = $captions, `tags` = $tags, " .
			   "`albums` = $albums WHERE id = " . $id;
		
		mysql_query($sql);
		
		return "Group " . $name . " edited.";
	}
	
	/**
	 * Get all groups
	 * @since 0.12.0
	 * 
	 * @return array Keys: id, name
	 */
	function getGroups(){
		$sql = "SELECT id, name FROM groups WHERE 1";
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_assoc($result)){
			$groups[] = array(  "id" => $row['id'],
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
	function getGroup($id){
		if(is_numeric($id)){
			$sql = "SELECT * FROM groups WHERE id = " . $id . " LIMIT 1";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
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
	function deleteGroup($id, $moveTo){
		if(is_numeric($id) && is_numeric($moveTo)){
			$group = $this->getGroup($id);
			
			$sql = "UPDATE users SET `group` = " . $moveTo . " WHERE `group` = " . $id;
			mysql_query($sql);
			
			$sql = "DELETE FROM groups WHERE id = " . $id;
			mysql_query($sql);
			
			return "Deleted " . $group['name'] . ".  Click <a href='" . createLink("manage") . "'>here</a> to continue managing groups.";
		}
		return "";
	}
	
	/**
	 * Make a select field for editing, auto select the right option
	 * @since 0.12.0
	 * 
	 * @param $name String name of select
	 * @param $option bool Which one to select
	 * 
	 * @return String Select box
	 */
	function createSelect($name, $option){
		$select = "<select name='" . $name ."'>";
		
		$select .= $option == 1 ? "<option value='0'>No</option><option value='1' selected>Yes</option>" : "<option value='0' selected>No</option><option value='1'>Yes</option>";
		$select .= "</select>";
		
		return $select;
	}
}