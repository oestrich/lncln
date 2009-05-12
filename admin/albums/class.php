<?
/**
 * class.php
 * 
 * Main class for the album admin module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

class AlbumsAdmin extends lncln{
	/**
	 * Adds an album to the database
	 * 
	 * @since 0.9.0
	 * @package lncln
	 * 
	 * @param string $name The name of the album
	 * 
	 * @return string If it passed or not
	 */
	function addAlbum($name){
		$name = prepareSQL($name);
		
		$sql = "INSERT INTO albums (name) VALUES (\"" . $name . "\")";
		mysql_query($sql);
		
		if(mysql_affected_rows() > 0){
			return "Add album " . $name . " successfully.";
		}
		else{
			return "Album not added";
		}
	}
	
	/**
	 * Deletes an album
	 * 
	 * @since 0.9.0
	 * @package lncln
	 * 
	 * @param int $album The album id to be deleted
	 */
	function deleteAlbum($album){
		$album = prepareSQL($album);
		
		if(is_numeric($album)){
			$sql = "UPDATE images SET album = 0 WHERE album = " . $album;
			mysql_query($sql);
			
			$sql = "DELETE FROM albums WHERE id = " . $album;
			mysql_query($sql);
		}		
	}
	
	/**
	 * Gets information about the album,
	 * name and id
	 * 
	 * @since 0.11.0
	 * @package lncln
	 * 
	 * @param $album int Album id
	 * 
	 * @return array
	 */
	function getAlbum($album){
		$album = prepareSQL($album);
		
		$sql = "SELECT id, name FROM albums WHERE id = " . $album;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		return $row;
		
	}
	
	/**
	 * Changes the album's name
	 * 
	 * @since 0.11.0
	 * @package lncln
	 * 
	 * @param $id int Album id
	 * @param $name string New name
	 */
	function changeAlbumName($id, $name){
		$id = prepareSQL($id);
		$name = prepareSQL($name);
		
		$sql = "UPDATE albums SET name = '" . $name ."' WHERE id = " . $id;
		mysql_query($sql);
	}
}
 
?> 