<?
/**
 * class.php
 * 
 * Main class for the group module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 
 
class Group extends lncln{

	/**
	 * Add a new group
	 * 
	 * @since 0.12.0
	 * @package lncln
	 * 
	 * @param $data array Info needed for a new group
	 */
	function addGroup($data){
		$data = array_map("prepareSQL", $data);
		
		$name = $data['name'];
		$index = $data['index'];
		$numIndex = $data['numIndex'];
		$report = $data['report'];
		$rate = $data['rate'];
		$obscene = $data['obscene'];
		$refresh = $data['refresh'];
		$delete = $data['delete'];
		$caption = $data['caption'];
		$tag = $data['tag'];
		$album = $data['album'];
		
		$sql = 	"INSERT INTO groups (name, index, numIndex, report, rate, obscene, refresh, delete, caption, tag, album) " .
				"VALUES ('$name', $index, $numIndex, $report, $rate, $obscene, $refresh, $delete, $caption, $tag, $album)";
		
		mysql_query($sql);
	}
	
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
}