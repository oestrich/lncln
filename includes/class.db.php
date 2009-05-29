<?php
/**
 * class.db.php
 * 
 * File containing the database class
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Database abstraction class
 * @since 0.13.0
 * 
 * @package lncln
 */
class Database{
	/**
	 * Contains database connection information
	 */
	private $config = array();
	
	/**
	 * The database connection
	 */
	private $conn;
	
	/**
	 * Stored queries
	 */
	public $queries = array();
	
	/**
	 * The last query performed
	 */
	public $last_query;
	
	/**
	 * Total number of queries performed on this page
	 */
	public $num_queries = 0;
	
	/**
	 * The MySQL result object
	 */
	public $result = null;
	
	/**
	 * Total length of queries on the page
	 */
	public $total_time = 0;
	
	/**
	 * Construct for database class, takes settings or uses config.php's ones
	 * @since 0.13.0
	 * 
	 * @param string $server Server
	 * @param string $username Username
	 * @param string $password Password
	 * @param string $database Database
	 */
	public function __construct($server = "", $username = "", $password = "", $database = ""){
		$this->config['server'] = $server == "" ? DB_SERVER : $server;
		
		$this->config['username'] = $username == "" ? DB_USERNAME : $username;
		
		$this->config['password'] = $password == "" ? DB_PASSWORD : $password;
		
		$this->config['database'] = $database == "" ? DB_DATABASE : $database;
	}

	/**
	 * Connects to the database
	 * @since 0.13.0
	 */
	public function connect(){
		$this->conn = @mysql_connect($this->config['server'], $this->config['username'], $this->config['password']);
		
		if(!$this->conn){
			echo mysql_error();
		}
		
		$this->select($this->config['database']);
	}

	/**
	 * Selects a database
	 * @since 0.13.0
	 * 
	 * @param string $db Database
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
	 * @param string|array $sql SQL
	 */
	public function query($sql){
		if(!$this->conn){
			$this->connect();
		}
		
		if(is_array($sql)){
			$sql = $this->create_sql($sql);
		}

		$this->start_timer();
		
		$this->clear_cache();
		
		$this->queries[] = $sql;
		$this->last_query = $sql;
		
		$this->num_queries += 1;
		
		$this->result = @mysql_query($sql, $this->conn);
		
		if(mysql_error($this->conn)){
			echo mysql_error($this->conn);
			echo "<br />";
			echo $sql;
			exit();
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
	 * Return one row from the previous query
	 * @since 0.13.0
	 * 
	 * @return array Next row
	 */
	public function fetch_one(){		
		$row = mysql_fetch_assoc($this->result);
		
		return $row;
	}
	
	/**
	 * Fetchs all of the rows related to the previous query
	 * @since 0.13.0
	 * 
	 * @param int $limit Number of rows to pull, 0 for all
	 * 
	 * @return array All rows
	 */
	public function fetch_all($limit = 0){
		if($this->fetched_results != null){
			return $this->fetched_results;
		}
		
		if(!$this->result){
			return array();
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
	 * Returns number of rows effected
	 * @since 0.13.0
	 * 
	 * @return int Number of rows, false if no query
	 */
	public function num_rows(){
		$num_rows = @mysql_num_rows($this->result);
		
		if(!$num_rows){
			return false;
		}
		
		return $num_rows;
	}
	
	/**
	 * Return the insert ID of previous query
	 * @since 0.13.0
	 * 
	 * @return int Insert ID
	 */
	public function insert_id(){
		return mysql_insert_id($this->conn);
	}
	
	/**
	 * Affected rows
	 * @since 0.13.0
	 * 
	 * @return int Number of rows affected
	 */
	public function affected_rows(){
		return mysql_affected_rows();
	}
	
	/**
	 * Creates SQL from provided array
	 * @since 0.13.0
	 *
	 * @param array $query Keys: 'type', 'where', 'order', 'fields', 'limit', 'table'
	 *
	 * @return string SQL
	 */
	public function create_sql($query){
		switch($query['type']){
			case "SELECT":
				$sql = $query['type'] . " ";
				$sql .= implode(", ", $this->grave_fields($query['fields']));
				$sql .= " FROM `" . $query['table'] . "`";
				
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
		}
	}
	
	/**
	 * Adds '`' around fields
	 * @since 0.13.0
	 *
	 * @param array $fields 
	 *
	 * @return array
	 */
	public function grave_fields($fields){
		foreach($fields as &$field){
			if($field == '*')
				continue;
			if(substr($field, 0, 1) == "!"){
				$field = substr($field, 1);
				continue;
			}
			$field = "`" . $field . "`";
		}
		
		return $fields;
	}
	
	/**
	 * Creates the WHERE section of SQL
	 * @since 0.13.0
	 *
	 * @param array $where Whole array section
	 *
	 * @return string Complete WHERE seciont
	 */
	public function create_where($where){
		if(!is_array($where)){
			return "";
		}
	
		$sql = $this->_where($where);
		$sql = $sql[0];
		
		if($sql == ""){
			return "";
		}
		if($sql == " (  ) "){
			return "";
		}
		
		return " WHERE " . $sql;
	}
	
	/**
	 * This is what does the dirty work for the where section
	 * @since 0.13.0
	 * 
	 * @param array $query Pieces of the where statement
	 * 
	 * @return array Final return is the final where string key 0
	 */
	private function _where($query){
		$sql = array();
		
		if(is_array($query)){
			foreach(array_keys($query) as $key){
				if(is_string($key)){
					$type = substr($key, 0, 2);
					switch($type){
						case 'AN':
							$sql[] = " ( " . implode(" AND ", $this->_where($query[$key])) . " ) ";
							break;
						case 'OR':
							$sql[] = " ( " . implode(" OR ", $this->_where($query[$key])) . " ) ";
							break;
						default:
							break;
					}
				}
				if(array_key_exists("field", $query[$key])){
					$string = "`" . $query[$key]['field'] . "` " . $query[$key]['compare'] . " ";
					$string .= is_numeric($query[$key]['value']) ? $query[$key]['value'] : "'" . $query[$key]['value'] . "'";
					
					$sql[] = $string;
				}
			}
		}
		
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
	
	/**
	 * Prepares SQL for querying
	 * @since 0.13.0
	 * 
	 * @param string $sql SQL
	 * 
	 * @return "clean" SQL
	 */
	public function prep_sql($sql){
		if(!$this->conn){
			$this->connect();
		}
		
		return mysql_real_escape_string($sql);
	}
	
	/**
	 * Print out total number of queries from a page
	 * @since 0.13.0
	 * 
	 * @return int Number of queries
	 */
	public function get_queries(){
		return $this->num_queries;
	}
}
