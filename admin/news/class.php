<?
/**
 * class.php
 * 
 * Main class for the News module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 
 
class News extends lncln{
	
	/**
	 * Adds a new news ticket type thing to the index
	 * 
	 * @since 0.11.0
	 * @package lncln
	 * 
	 * @param $news array Keys - title, body
	 */
	function addNews($news){
		$body = prepareSQL($news['body']);
		
		$sql = "INSERT INTO news (postTime, news) VALUES (" . time() . ", '" . $body . "')";
		mysql_query($sql);
		
		return "News added.";
	}
	
	/**
	 * Returns all news
	 * 
	 * @since 0.11.0
	 * @package lncln
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
}