<?
/**
 * class.php
 * 
 * Main class for the News module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */ 
 
class NewsAdmin extends lncln{
	
	/**
	 * Adds a new news ticket type thing to the index
	 * @since 0.11.0
	 * 
	 * @param $news array Keys - title, body
	 */
	function addNews($news){
		$body = $this->db->prep_sql($news['body']);
		$title = $this->db->prep_sql($news['title']);
		
		$sql = "INSERT INTO news (postTime, title, news) VALUES (" . time() . ", '" . $title . "', '" . $body . "')";
		mysql_query($sql);
		
		return "News added.";
	}
	
	/**
	 * Returns all news
	 * @since 0.11.0
	 * 
	 * @return array Keys- id, postTime, title, news
	 */
	function getNews(){
		$news = array();
		
		$sql = "SELECT id, postTime, title, news FROM news WHERE 1";
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_assoc($result)){
			$news[] = $row;
		}
		
		return $news;
	}
	
	/**
	 * Delete a news item
	 * @since 0.11.0
	 * 
	 * @param $id int Item to be deleted
	 */
	function deleteNews($id){
		if(is_numeric($id)){
			$sql = "DELETE FROM news WHERE id = " . $id;
			mysql_query($sql);
		}
	}
	
	/**
	 * Fetch one news story
	 * @since 0.11.0
	 * 
	 * @param $id int Item to be found
	 * 
	 * @return array Keys- id, postTime, title, news
	 */
	function getNewsOne($id){
		if(is_numeric($id)){
			$sql = "SELECT id, postTime, title, news FROM news WHERE id = " . $id;
			$result = mysql_query($sql);
			
			if(mysql_num_rows($result) == 1)
				return mysql_fetch_assoc($result);
		}
	}
	
	/**
	 * Edit's news items
	 * @since 0.12.0
	 * 
	 * @param $data array Keys: title, news, postTime
	 */
	function changeNews($data){
		$title = $this->db->prep_sql($data['title']);
		$news = $this->db->prep_sql($data['news']);
		
		if(is_numeric($data['postTime']) && is_numeric($data['id'])){
			$postTime = $data['postTime'];
			$id = $data['id'];
		}
		else{
			return "";
		}
		
		$sql = "UPDATE news SET title = '" . $title . "', news = '" . $news ."', postTime = " . $postTime . " WHERE id = " . $id;
		mysql_query($sql);
	}
}