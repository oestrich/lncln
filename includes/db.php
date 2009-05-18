<?php
/**
 * db.php
 * 
 * database class
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */


class Database{
	private $conn;
	
	public $last_query;
	public $num_queries = 0;
	
	public $result;
	public $fetched_results = null; //Cached results
	
	public $total_time = 0;

	/**
	 * Connects to the database
	 * @since 0.13.0
	 * 
	 * @param $host string Hose
	 * @param $user string Username
	 * @param $password string Password
	 * @param $db string Database
	 */
	public function __construct($host, $user, $password, $db){
		$this->conn = @mysql_connect($host, $user, $password);
		
		if(!$this->conn){
			echo mysql_error();
		}
		
		$this->select($db);
	}
	
	/**
	 * Selects a database
	 * @since 0.13.0
	 * 
	 * @param $db string Database
	 */
	public function select($db){
		if(!@mysql_select_db($db, $this->conn)){
			echo "Bad database";
			exit();
		}
	}
	
	/**
	 * Queries the data base, if $sql is an array it calls create_sql
	 * @since 0.13.0
	 * 
	 * @param $sql string|array SQL
	 */
	public function query($sql){
		$this->start_timer();
		
		$this->clear_cache();
		
		if(is_array($sql)){
			$sql = $this->create_sql($sql);
		}
	
		$this->last_query = $sql;
		$this->num_queries += 1;
		
		$this->result = @mysql_query($sql, $this->conn);
		
		if(mysql_error($this->conn)){
			echo mysql_error($this->conn);
		}
		
		$this->stop_timer();
	}
	
	/**
	 * Clears the previous queries
	 * @since 0.13.0
	 */
	public function clear_cache(){
		$this->fetched_results = null;
	}
	
	/**
	 * Fetchs all of the rows related to the previous query
	 * @since 0.13.0
	 * 
	 * @param $limit int Number of rows to pull, 0 for all
	 * 
	 * @return array All rows
	 */
	public function fetch($limit = 0){
		if($this->fetched_results != null){
			return $this->fetched_results;
		}
	
		$rows = array();
		
		$i = 0;
		while($row = mysql_fetch_assoc($this->result)){
			$i++;
			
			if($limit == $i)
				return $rows;
			
			$rows[] = $row;
		}
		
		$this->fetched_results = $rows;
		
		return $rows;
	}
	
	/**
	 * Creates SQL from provided array
	 * @since 0.13.0
	 *
	 * @param $query array Keys: 'type', 'where', 'order', 'fields', 'limit', 'table'
	 *
	 * @return string SQL
	 */
	public function create_sql($query){
		switch($query['type']){
			case "SELECT":
				$sql = $query['type'] . " ";
				$sql .= implode(", ", $this->grave_fields($query['fields']));
				$sql .= " FROM `" . $query['table'] . "`";
				
				if(!is_array($query['where']))
					$query['where'] = array();
				
				$sql .= $this->create_where($query['where']);
				
				if(is_array($query['order'])){
					$sql .= " ORDER BY " . implode(", ", $this->grave_fields($query['order'][1])) .
						" " . $query['order'][0];
				}
				
				if(is_array($query['limit'])){
					$sql .= " LIMIT " . $query['limit'][0];

					if(isset($query['limit'][1]))
						$sql .= ", " . $query['limit'][1];
				}
				
				return $sql;
				break;
		}
	}
	
	/**
	 * Adds '`' around fields
	 * @since 0.13.0
	 *
	 * @param $fields array
	 *
	 * @return array
	 */
	public function grave_fields($fields){
		foreach($fields as &$field){
			if($field == '*')
				continue;
			$field = "`" . $field . "`";
		}
		
		return $fields;
	}
	
	/**
	 * Creates the WHERE section of SQL
	 * @since 0.13.0
	 *
	 * @param $where array Keys: 'field', 'compare', 'value'
	 *
	 * @return string Complete WHERE seciont
	 */
	public function create_where($where){
		$sql = array();
		
		$i = 0;
		foreach($where as $value){
			$sql[$i] = "`" . $value['field'] . "` " . $value['compare'] . " ";
			if(is_numeric($value['value'])){
				$sql[$i] .= $value['value'];
			}
			else{
				$sql[$i] .= "'" . $value['value'] ."'";
			}
			$i++;
		}
		
		$sql = " WHERE " . implode(" AND ", $sql);
		
		return $sql;
	}
	
	/**
	 * Starts the timer
	 * @since 0.13.0
	 */
	public function start_timer(){
		$this->time_start = microtime(true);
	}
	
	/**
	 * Stops the timer and adds to the total
	 * @since 0.13.0
	 */
	public function stop_timer(){
		$this->total_time += microtime(true) - $this->time_start;
	}
}
