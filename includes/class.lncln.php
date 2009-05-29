<?php
/**
 * class.lncln.php
 * 
 * Main class for lncln
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
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
	 * Gets the class ready for action!
	 * @since 0.6.0
	 * 
	 * @param string $action Which type of page is being loaded
	 * @param array $params any extra parameters that will be passed onto the action
	 */
	function __construct($action = "none", $params = array()){	
		//Pull in the database class that was already started.
		$this->db = get_db();
		
		$this->user = new MainUser();
		$this->display = new Display($this);
		$this->loadModules();
		
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
	}
	
	/**
	 * Loads parameters set by the URL
	 * @since 0.13.0
	 */
	function loadParams(){
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
	function loadModules(){
		global $modules_enabled;
		
		$this->modules_enabled = $modules_enabled;
		
		$this->display->rows = array(
			1 => array("index", "albums", "obscene"),
			3 => array("admin", "queue"),
			4 => array("tags")
		);
		
		foreach($this->modules_enabled as $folder => $class){
			/** Include the main class file for modules */
			include_once(ABSPATH . "modules/" . $folder . "/" . $folder . ".class.php");
			/** Include the info file for modules */
			include_once(ABSPATH . "modules/" . $folder . "/" . $folder . ".info.php");
			$this->modules[$folder] = new $class($this);
		}
	}
	
	/**
	 * Function for loading the queue
	 * @todo move into the Queue module
	 * @since 0.9.0
	 */
	function queue(){
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
		if($row['COUNT(*)'] > 0){
			$this->db->query("SELECT COUNT(id) FROM images WHERE queue = 1");
			$row = $this->db->fetch_one();

			$this->maxPage = $row['COUNT(id)'];
			$this->maxPage = ceil($this->maxPage / $this->display->settings['perpage']);
			
			if(!isset($_GET['page'])){
				$this->page = 1;
			}
			else{
				if(is_numeric($_GET['page'])){
					$this->page = $_GET['page'];	
				}
				else{
					$this->page = 1;
				}
			}
			
			$offset = ($this->page - 1) * $this->display->settings['perpage'];
			
			$sql = "SELECT id FROM `images` WHERE queue = 1 ORDER BY id DESC LIMIT " . $offset . ", " . $this->display->settings['perpage'];
			$this->db->query($sql);
			
			foreach($this->db->fetch_all() as $row){				
				$this->imagesToGet[] = $row['id'];
			}
		}
	}
	
	/**
	 * First person to send an email to codecomments@lncln.com gets the
	 * chance to maybe win $1 million.  Ok, that's a lie.  But I will post you
	 * on the homepage.
	 */
	
	/**
	 * Gets data ready for the rss feed
	 * @todo move into the RSS module
	 * @since 0.9.0
	 * 
	 * @param array $rss First term is the type of rss feed (all/safe)
	 */
	function rss($rss){
		$safe = $rss[0] != "all" ? " AND obscene = 0" : "";
		
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 0 " . $safe;
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
		if($row['COUNT(*)'] > 0){
			$sql = "SELECT id FROM images WHERE queue = 0 AND postTime <= " . time() . " " . $safe . " ORDER BY `id` DESC LIMIT " . $this->display->settings['perpage'];
			$this->db->query($sql);
			
			foreach($this->db->fetch_all() as $row){
				$this->imagesToGet[] = $row['id'];
			}
		}
	}
	
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
	function thumbnail($img){
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
			$command = "convert -resize '" . $thumb . "' -quality 35 " . ABSPATH . "images/full/" . $img . "[0] " . ABSPATH . "images/thumb/" . $img . ".jpg";
			exec($command);
			
			$command = "convert " . ABSPATH . "images/thumb/" . $img . ".jpg " . ABSPATH . "images/thumb/" . $img;
		}
		else{
			$command = "convert -resize '" . $thumb . "' -quality 35 " . ABSPATH . "images/full/" . $img . " " . ABSPATH . "images/thumb/" . $img;
		}
		exec($command);
		
		if($type == "gif"){
			$command = "convert -resize '" . $norm . "' -quality 35 " . ABSPATH . "images/full/" . $img . "[0] " . ABSPATH . "images/index/" . $img . ".jpg";		
			exec($command);
			
			$command = "convert " . ABSPATH . "images/index/" . $img . ".jpg " . ABSPATH . "images/index/" . $img;
		}
		else{
			$command = "convert -resize '" . $norm . "' -quality 35 " . ABSPATH . "images/full/" . $img . " " . ABSPATH . "images/index/" . $img;
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
	function get_data(){		
		if(count($this->imagesToGet) == 0){
			$this->images = array();
			return;
		}
		
		$time = !$this->user->permissions['isAdmin'] ? array('field' => 'postTime', 'compare' => '<=', 'value' =>time()) : array();
		
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
	 * Creates the Prev Next links on the page
	 * @todo Rename to prev_next()
	 * @since 0.5.0
	 * 
	 * @param $bottom bool If it's a bottom link
	 * 
	 * @return string Contains the links Prev Next
	 */
	function prevNext($bottom = false){
		$extra = $this->type == "thumb" ? "&amp;thumb=true" : "";
		
		$script = URL . $this->module . "/";

		$tempParams = $this->params;
		array_pop($tempParams); // Remove page
		$tempParams = join($tempParams, "/");
		
		if(substr($tempParams, -1) != "/" && $tempParams != "")
			$tempParams .= "/";
		
		$script .= $tempParams;
		
		$output = $bottom == true ? "<div id='bPrevNext'>" : "<div class='prev_next'>";
		
		if ($this->page == 1 && $this->page != $this->maxPage){
	        $output .= "<a href='" . $script . ($this->page + 1) . "' >Next page</a>";
	    }
	    elseif(($this->page == 1 && $this->page == $this->maxPage) || $this->page == 0){
	    	$output .= "";
	    }
	    elseif($this->page == $this->maxPage){
	        $output .= "<a href='" . $script . ($this->page - 1) . "' >Prev page</a>";
	    }
	    else{
	        $output .= "<a href='" . $script . ($this->page - 1) . "' >Prev page</a>
	        <a href='" . $script . ($this->page + 1) . "' >Next page</a>";
	    }
	    
	    $output .= $bottom == true ? "</div>" : "</div>";
	    
	    return $output;
	}

	/**
	 * The first part of uploading, moves the image to a temporary spot
	 * This allows for taging, captions, etc
	 * @todo Move to Upload module
	 * @since 0.10.0
	 */
	function tempUpload(){
	 	$_SESSION['uploaded'] = true;
		$_SESSION['pages'] = 0;
	 	
	 	for($i = 0; $i < 10; $i++){
	 		//if nothing in either style uploads
			if($_POST['upload' . $i] == "" && $_FILES['upload'.$i]['name'] == ""){
				$_SESSION['upload'][$i] = 0;
				continue;
			}
			
			//Splitting the entire name, so it can pull the extension next
			$typeTmp = $_GET['url'] ? split("\.", $_POST['upload' . $i]) : split("\.", $_FILES['upload'.$i]['name']);
			
	        //the file extension
			$type = $typeTmp[count($typeTmp) - 1];
			
			//only these types
			if($type == "png" || $type == "jpg" || $type == "gif"){
				if($_GET['url']){
					$file = @file_get_contents($_POST['upload' . $i]);
					if(!$file){
						$_SESSION['upload'][$i] = 5;
						$this->uploaded[] = array("error" => "404", "image" => $_POST['upload' . $i]);
						continue;
					}
					$tempName = split("\/", $_POST['upload' . $i]);

					$tempName = $tempName[count($tempName) - 1];
					
					$name = tempName($tempName);
				}
				else{
					$name = tempName($_FILES['upload' . $i]['name']);
				}
				
				if($_GET['url']){
					file_put_contents(CURRENT_IMG_TEMP_DIRECTORY . $name, $file);
				}
				else{
					move_uploaded_file($_FILES['upload'.$i]['tmp_name'], CURRENT_IMG_TEMP_DIRECTORY . $name);
				}
				
				$this->uploaded[] = $name;
				
				$_SESSION['uploadKey'][$name] = $i;
			}
			else{
				$_SESSION['upload'][$i] == 4;
			}
	 	}
	 }

	/**
	 * Uploads the pictures that the user fills in.  Whether it be from a URL or 
	 * direct input.
	 * @todo Move to Upload module
	 * @since 0.5.0
	 */
	function upload($name, $data){
		if($data['tags'] == ""){
			$_SESSION['upload'][$_SESSION['uploadKey'][$name]] = 3;
			unlink(CURRENT_IMG_TEMP_DIRECTORY . $name);
			return "";
		}
		
		$sql = "SELECT MAX(postTime) FROM images";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
					
		$postTime = time() >= ($row['MAX(postTime)'] + (60 * $this->display->settings['tbp'])) ? time() : $row['MAX(postTime)'] + (60 * $this->display->settings['tbp']);

		$this->user->checkUploadLimit(true);

		$typeTmp = split("\.", $name); 
		$type = $typeTmp[count($typeTmp) - 1];

		if($this->user->permissions['toQueue'] == 0){
			$sql = "INSERT INTO images (postTime, type, queue) VALUES (" . $postTime . ", '" . $type . "', 0)";
			$_SESSION['upload'][$_SESSION['uploadKey'][$name]] = 1;
		}
		else{
			$sql = "INSERT INTO images (postTime, type) VALUES (" . $postTime . ", '" . $type . "')";
			$_SESSION['upload'][$_SESSION['uploadKey'][$name]] = 2;
		}
		
		$_SESSION['uploadTime'][$_SESSION['uploadKey'][$name]] = $postTime;
		
		$this->db->query($sql);
		
		$imgID = str_pad($this->db->insert_id(), 6, 0, STR_PAD_LEFT);
				
		$_SESSION['image'][$_SESSION['uploadKey'][$name]] = $imgID . '.' . $type;
		
		rename(CURRENT_IMG_TEMP_DIRECTORY . $name, CURRENT_IMG_DIRECTORY . $imgID . '.' . $type);
		
		foreach($this->modules as $key => $module){
			if(method_exists($module, "add")){
				$module->add($imgID, array($data[$key]));
			}
		}
		
		$this->thumbnail($imgID . '.' . $type);
	}
	
	/**
	 * Removes an image from the queue
	 * @todo Move to Queue module
	 * @since 0.5.0
	 * 
	 * @param int $image The image that is to be removed
	 */
	function dequeue($image){
		$id = $this->db->prep_sql($image);
		
		$sql = "UPDATE images SET queue = 0, report = 0 WHERE id = " . $id . " LIMIT 1";
		$this->db->query($sql);
	}
		
	/**
	 * Removes an image.  First deletes the image from sql and then unlinks
	 * the image itself and then the two thumbnails
	 * @since 0.5.0
	 * 
	 * @param int $image The image to be deleted
	 * 
	 * @return string Whether it deleted it or not
	 */
	function delete($image){
		$sql = "SELECT type FROM images WHERE id = " . $image . " LIMIT 1";
		$this->db->query($sql);
		if($this->db->num_rows() == 1){
			$type = $this->db->fetch_one();
		}
		else{
			return "No such image.";
		}
	
		$sql = "DELETE FROM images WHERE id = " . $image . " LIMIT 1";
		mysql_query($sql);
		
		//use and @ sign so that it won't throw an error, probably meaning it wasn't there to begin with
		@unlink(ABSPATH . "images/full/" . $image . "." . $type['type']);
		@unlink(ABSPATH . "images/thumb/" . $image . "." . $type['type']);
		@unlink(ABSPATH . "images/index/" . $image . "." . $type['type']);
		
		return "Successfully deleted.";
	}

	/**
	 * Debug function to print out the private variables;
	 * @since 0.9.0
	 */
	function debug(){		
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
	function increaseView($image){
		if(is_numeric($image)){
			$sql = "UPDATE images SET view = view + 1 WHERE id = " . $image;
			$this->db->query($sql);
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
	function getNews(){
		$sql = "SELECT news, postTime, title FROM `news` ORDER BY id DESC LIMIT 1";
		$this->db->query($sql);
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
	function getImagePath($id, $size = "full"){
		if(!is_numeric((int)$id))
			return "";
		
		$sql = "SELECT type FROM images WHERE id = " . $id . " LIMIT 1";
		$this->db->query($sql);
		
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
	function check_small($id){
		foreach($this->modules as $module){
			if(method_exists($module, "small")){
				if($module->small($id) == true){
					return true;
				}
			}
		}
		
		return false;
	}
}
