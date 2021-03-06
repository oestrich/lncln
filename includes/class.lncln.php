<?php
/**
 * class.lncln.php
 * 
 * Main class for lncln
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * The main class for lncln.  Shall handle most features.
 * @since 0.6.0
 * 
 * @package lncln
 */
 
class lncln{
	/**
	 * @var Database Reference to the Database instance
	 */
	public $db;
	
	/**
	 * @var MainUser Reference to the MainUser instance
	 */
	public $user;
	
	/**
	 * @var bool If the moderation link will show
	 */
	public $moderationOn = false;
	
	/**
	 * @var string Accessed page
	 */
	public $script;
	
	/**
	 * @var Display Reference to the Display instance
	 */
	public $display;

	/**
	 * @var int Current page
	 */
	public $page;
	
	/**
	 * @var int Total number of pages
	 */
	public $maxPage;
	
	/**
	 * @var array
	 */
	public $uploaded = array();
	
	/**
	 * @var array IDs of images for get_data() to pull
	 */
	public $imagesToGet = array();
	
	/**
	 * @var array Data for the images
	 */
	public $images = array();
	
	/**
	 * @var string Type of page: normal, thumb
	 */
	public $type;
	
	/**
	 * @var array Array of instances of modules
	 */
	public $modules = array();
	
	/**
	 * @var string Currently accessed module
	 */
	public $module;
	
	/**
	 * @var array Anything that comes after the module in the URL
	 */ 
	public $params = array();
	
	/**
	 * @var bool If an action is to be called
	 */
	public $action = false;
	
	/**
	 * @var array Settings for lncln
	 */
	public $settings = array();
	
	/**
	 * Gets the class ready for action!
	 * @since 0.6.0
	 * 
	 * @param string $action Which type of page is being loaded
	 * @param array $params any extra parameters that will be passed onto the action
	 */
	public function __construct($action = "none", $params = array()){	
		//Pull in the database class that was already started.
		$this->db = get_db();
		
		$this->user = new MainUser();
		$this->display = new Display($this);
		
		$this->script = split("/", $_SERVER['SCRIPT_NAME']);
		$this->script = $this->script[count($this->script) - 1];
		
		$this->loadParams();
		
		if(isset($_GET['module']) && $_GET['module'] != ""){
			if ($_GET['module'] == "action"){				
				$this->module = array_shift($this->params);
				
				$this->action = true;
			}
			elseif ($_GET['module'] == "thumbnail"){
				$this->module = "thumbnail";
			}
			else{			
				$this->module = $_GET['module'];
				$_SESSION['URL'] = $this->module . "/" . join($this->params, "/");
			}
			
			$_SESSION['URL'] = str_replace(" ", "+", $_SESSION['URL']);
		}
		else{
			$this->module = "index";
			$_SESSION['URL'] = $this->module;
		}
		
		$this->settings_load();
	}
	
	/**
	 * Loads parameters set by the URL
	 * @since 0.13.0
	 */
	public function loadParams(){
		$q = $_GET['q'];
		
		$this->params = split("/", $q);
		
		//Switches spaces with +
		foreach ($this->params as &$param){
			if (substr_count($param, "+") > 1)
				$param = str_replace(" ", "+", $param);		
		}
	}
	
	/**
	 * Loads enabled modules
	 * @todo make modules configurable by the admin panel
	 * @since 0.13.0
	 */
	public function loadModules(){		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('class', 'folder'),
			'table' => 'modules',
			'where' => array(
				array(
					'field' => 'enabled',
					'compare' => '=',
					'value' => 1,
					),
				),
			);
		
		$this->db->query($query);
		
		$this->display->rows = array(
			1 => array("index", "albums", "obscene", "user"),
			2 => array("upload"),
			3 => array("admin", "queue"),
			4 => array("tags"),
		);
		
		foreach($this->db->fetch_all() as $module){
			$this->modules_enabled[$module['folder']] = $module['class'];
		}
		
		foreach($this->modules_enabled as $folder => $class){
			/** Include the main class file for modules */
			include_once(ABSPATH . "modules/" . $folder . "/" . $folder . ".class.php");
			/** Include the info file for modules */
			include_once(ABSPATH . "modules/" . $folder . "/" . $folder . ".info.php");
			$this->modules[$folder] = new $class();
		}
	}
	
	/**
	 * First person to send an email to codecomments@lncln.com gets the
	 * chance to maybe win $1 million.  Ok, that's a lie.  But I will post you
	 * on the homepage.
	 */
	
	/**
	 * Creates thumbnails for the site.  Uses ImageMagick.  For gifs
	 * it has to do a temporary jpeg and then back to gif.
	 * 
	 * Should really make it so that if ImageMagick isn't installed
	 * it doesn't die.
	 * @todo move into the Upload module
	 * @since 0.5.0
	 * 
	 * @param string $img String containing the filename of the image
	 */
	public function thumbnail($img){
		$size = getimagesize(CURRENT_IMG_DIRECTORY . $img);
		
		$type = split("\.", $img);
		$type = $type[count($type) - 1];
		
		$tHeight = ($size[1] / $size[0]) * 150;
	
		if($size[1] > 600 || $size[0] > 600){
			$norm = "600x" . $size[1];
		}
		else{
			$norm = $size[0] . "x" . $size[1];
		}
	
		if($tHeight > 150){
			$thumb =  $size[0] . "x150";
		}else{
			$thumb = "150x" . $size[1];
		}
	
		if($type == "gif"){
			$command = "convert -resize '" . $thumb . "' -quality 35 " . ABSPATH . 
						"images/full/" . $img . "[0] " . ABSPATH . "images/thumb/" . $img . ".jpg";
			exec($command);
			
			$command = "convert " . ABSPATH . "images/thumb/" . $img . ".jpg " . ABSPATH . 
						"images/thumb/" . $img;
		}
		else{
			$command = "convert -resize '" . $thumb . "' -quality 35 " . ABSPATH . 
						"images/full/" . $img . " " . ABSPATH . "images/thumb/" . $img;
		}
		exec($command);
		
		if($type == "gif"){
			$command = "convert -resize '" . $norm . "' -quality 35 " . ABSPATH . 
						"images/full/" . $img . "[0] " . ABSPATH . "images/index/" . $img . ".jpg";		
			exec($command);
			
			$command = "convert " . ABSPATH . "images/index/" . $img . ".jpg " . 
						ABSPATH . "images/index/" . $img;
		}
		else{
			$command = "convert -resize '" . $norm . "' -quality 35 " . ABSPATH . 
						"images/full/" . $img . " " . ABSPATH . "images/index/" . $img;
		}
		exec($command);
		
		if($type == "gif"){
			unlink(ABSPATH . "images/index/" . $img . ".jpg");
			unlink(ABSPATH . "images/thumb/" . $img . ".jpg");
		}
	}

	/**
	 * Creates the data required for listImages.php
	 * @since 0.5.0
	 */
	public function get_data(){		
		if(count($this->imagesToGet) == 0){
			$this->images = array();
			return;
		}
		
		$time = !$this->user->permissions['isAdmin'] ? 
			array('field' => 'postTime', 'compare' => '<=', 'value' =>time()) : array();
		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('*'),
			'table' => 'images',
			'where' => array(
				'AND' => array(
					'OR' => array(),
					$time,
					),
				),
			'order' => array(
					'DESC',
					array('id'),
				),
			);

		foreach($this->modules as $module){
			if(method_exists($module, "get_data_sql")){
				$query['where']['AND'][] = $module->get_data_sql();
			}
		}
		
		foreach($this->imagesToGet as $image){
			$query['where']['AND']['OR'][] = array(
				'field' => 'id',
				'compare' => '=',
				'value' => $image,
				); 
		}
		
		$this->db->query($query);
		
		$i = 0;
		foreach($this->db->fetch_all() as $image){						
			$this->images[$i] = array(
				'id' 		=> $image['id'],
				'file' 		=> $image['id'] . "." . $image['type'],
				'type'		=> $image['type'],
				'postTime'	=> $image['postTime'],
				'caption'	=> $image['caption'],
				);
			foreach($image as $key => $field){
				if(!array_key_exists($key, $this->images[$i])){
					$this->images[$i][$key] = $field; 
				}
			}
			$i++;
		}
		
		$this->type = "index";
		
		if($_SESSION['thumbnail'] == 1){
			$this->type = "thumb";
		}
	}
	
	/**
	 * Returns current number of images
	 * @since 0.13.0
	 */
	public function get_num_images(){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('!COUNT(id) as total'),
			'table' => 'images',
			'where' => array(
				'AND' => array(
					array(
						'field' => 'postTime',
						'compare' => '<=',
						'value' => time(),
						),
					),
				),
			);
		
		foreach($this->modules as $module){
			if(method_exists($module, "get_data_sql")){
				$query['where']['AND'][] = $module->get_data_sql();
			}
		}
		
		$this->db->query($query);
		$result = $this->db->fetch_one();
		
		return $result['total'];
	}

	/**
	 * Creates the Prev Next links on the page
	 * @since 0.5.0
	 * 
	 * @param $bottom bool If it's a bottom link
	 * 
	 * @return string Contains the links Prev Next
	 */
	public function prev_next($bottom = false){
		$extra = $this->type == "thumb" ? "&amp;thumb=true" : "";
		
		$script = URL . $this->module . "/";

		$tempParams = $this->params;
		array_pop($tempParams); // Remove page
		$tempParams = join($tempParams, "/");
		
		if(substr($tempParams, -1) != "/" && $tempParams != "")
			$tempParams .= "/";
		
		$script .= str_replace(" ", "+", $tempParams);
		
		$output = $bottom == true ? "<div id='bPrevNext'>\n" : "<div class='prev_next'>\n";
		
		if ($this->page == 1 && $this->page != $this->maxPage){
	        $output .= "<a href='" . $script . ($this->page + 1) . "' >Next page</a>\n";
	    }
	    elseif(($this->page == 1 && $this->page == $this->maxPage) || $this->page == 0){
	    	$output .= "";
	    }
	    elseif($this->page == $this->maxPage){
	        $output .= "<a href='" . $script . ($this->page - 1) . "' >Prev page</a>\n";
	    }
	    else{
	        $output .= "<a href='" . $script . ($this->page - 1) . "' >Prev page</a>\n" . 
	        			"<a href='" . $script . ($this->page + 1) . "' >Next page</a>";
	    }
	    
	    $output .= $bottom == true ? "</div>\n" : "</div>\n";
	    
	    return $output;
	}
	
	/**
	 * Temporary link function for old prev_next() name
	 * @deprecated 
	 * 
	 * @param $bottom bool If it's a bottom link
	 * 
	 * @return string Contains the links Prev Next
	 */
	public function prevNext($bottom = false){
		return $this->prev_next($bottom);
	}

	/**
	 * Removes an image from the queue
	 * @todo Move to Queue module
	 * @since 0.5.0
	 * 
	 * @param int $image The image that is to be removed
	 */
	public function dequeue($image){
		$id = $this->db->prep_sql($image);
		
		$query = array(
			'type' => 'UPDATE',
			'table' => 'images',
			'set' => array(
				'queue' => 0,
				'report' => 0,
				),
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
	}

	/**
	 * Debug function to print out the private variables;
	 * @since 0.9.0
	 */
	public function debug(){		
		echo "script: " . $this->script . "\n";

		echo "page: " . $this->page . "\n";
		echo "maxPage: " . $this->maxPage . "\n";
		
		echo "imagesToGet: ";
		print_r($this->imagesToGet);
		
		echo "images: ";
		print_r($this->images);
		echo "type: " . $this->type . "\n";
		echo "extra: " . $this->extra . "\n";
	}
	
	/**
	 * Increase the view count of an image
	 * @since 0.12.0
	 * 
	 * @param $image int Id of image
	 */
	public function increaseView($image){
		if(is_numeric($image)){
			$query = array(
				'type' => 'UPDATE',
				'table' => 'images',
				'set' => array(
					'view' => '!`view` + 1',
					),
				'where' => array(
					array(
						'field' => 'id',
						'compare' => '=',
						'value' => $image,
						),
					),
				);
			
			$this->db->query($query);
		}
	}
		
	/**
	 * Returns the latest news
	 * @todo Move to News module
	 * @todo Rename to get_news()
	 * @since 0.12.0
	 * 
	 * @return string The Latest news
	 */
	public function getNews(){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('news', 'postTime', 'title'),
			'table' => 'news',
			'order' => array('DESC', array('id')),
			'limit' => array(1),
			);
		
		$this->db->query($query);
		
		$row = $this->db->fetch_one();
		
		return $row;
	}
	
	/**
	 * Return the full path of an image
	 * @todo Rename to get_image_path()
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * @param string $size Size of image requested
	 * 
	 * @return string URL to image requested
	 */
	public function getImagePath($id, $size = "full"){
		if(!is_numeric((int)$id))
			return "";
		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('type'),
			'table' => 'images',
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
		
		if($this->db->num_rows() == 1){
			$row = $this->db->fetch_one();
			$type = $row['type'];
			
			return "http://" . SERVER . URL ."images/$size/$id.$type";
		}
		
		return "";
	}
	
	/**
	 * Check to see if image needs to be shrunk
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return bool True if image is small
	 */
	public function check_small($id){
		foreach($this->modules as $module){
			if(method_exists($module, "small")){
				if($module->small($id) == true){
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Load settings from the database
	 * @since 0.13.0
	 */
	public function settings_load(){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('name', 'value'),
			'table' => 'settings',
			);
		
		$this->db->query($query);
		
		foreach($this->db->fetch_all() as $row){
			$this->settings[$row['name']] = unserialize($row['value']);
		}
	}
	
	/**
	 * Change a setting for lncln
	 * @since 0.13.0
	 * 
	 * @param string $name Setting name
	 * @param mixed $value Value to be saved
	 */
	public function setting_set($name, $value){
		$value = serialize($value);
		
		if(isset($this->settings[$name])){
			$query = array(
				'type' => 'UPDATE',
				'table' => 'settings',
				'set' => array(
					'value' => $value,
					),
				'where' => array(
					array(
						'field' => 'name',
						'compare' => '=',
						'value' => $name,
						),
					),
				);
			
			$this->db->query($query);
		}
		else{
			$query = array(
				'type' => 'INSERT',
				'table' => 'settings',
				'fields' => array('name', 'value'),
				'values' => array(
					array($name, $value),
					),
				);
			
			$this->db->query($query);
		}
		
		$this->settings[$name] = unserialize($value);
	}
}
