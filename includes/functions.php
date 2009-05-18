<?
/**
 * functions.php
 * 
 * One of the most important files, should be included in every page
 * Contains the main class lncln, as well as other functions
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0  $Id$
 * @license license.txt GNU General Public License version 3
 */
 
/**
 * The main class for lncln.  Shall handle most features.  
 * 
 * @since 0.6.0
 * 
 * @param string $action The action you wish to perform
 * @param array $params The parameters to be sent to the action
 */
 
class lncln{
	public $db;
	
	public $user;
	public $moderationOn = false;
	
	public $script;				//what page is being loaded
	public $display;			//The Display class, controlls settings

	public $page;				//The page you are on
	public $maxPage;			//Total number of pages
	
	public $uploaded = array();
	
	public $imagesToGet = array();		//The images that data will be pulled for
	public $images = array(); 	//Image data to be outputed in listImages.php
	public $type;				//Normal or thumb
	
	public $modules = array();
	public $module;				//Currently used module ex. ?module=tags 
	public $params = array();
	
	public $action = false; //bool
	
	/**
	 * Gets the class ready for action!
	 * @since 0.6.0
	 * 
	 * @param string $action Which type of page is being loaded
	 * @param array $params any extra parameters that will be passed onto the action
	 */
	function __construct($action = "none", $params = array()){	
		//Pull in the database class that was already started.
		global $db;
		$this->db = $db;
		
		$this->user = new User();
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
		//Key is folder, value is class name
		$this->modules_enabled = array(
			"captions" => "Captions", 
			"tags" => "Tags", 
			"albums" => "Albums", 
			"ratings" => "Ratings", 
			"index" => "Index", 
			"image" => "Image",
			"admin" => "Admin",
			"obscene" => "Obscene",
		);
		
		$this->display->rows = array(
			1 => array("index", "albums", "obscene"),
			3 => array("admin"),
			4 => array("tags")
		);
		
		foreach($this->modules_enabled as $folder => $class){
			include_once(ABSPATH . "modules/" . $folder . "/class.php");
			include_once(ABSPATH . "modules/" . $folder . "/info.php");
			$this->modules[$folder] = new $class($this);
		}
	}
	
	/**
	 * Function for loading the queue
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
	 * @todo possibly rename to getData()
	 * @since 0.5.0
	 */
	function img(){		
		if(count($this->imagesToGet) == 0){
			$this->images = array();
			return;
		}
		
		$time = $this->user->permissions['isAdmin'] == 0 ? " AND postTime <= " . time() : "";
		
		$sql = "SELECT id, caption, postTime, type FROM images WHERE ";
		
		foreach($this->imagesToGet as $image){
			$sql .= " id = " . $image . " OR ";
		}
		
		if(count($this->imagesToGet) > 0)
			$sql = substr_replace($sql, "", -4);
			
		$sql .= $time;
		$sql .= " ORDER BY `id` DESC";
		
		$this->db->query($sql);
		
		foreach($this->db->fetch_all() as $image){						
			$this->images[] = array(
				'id' 		=> $image['id'],
				'file' 		=> $image['id'] . "." . $image['type'],
				'type'		=> $image['type'],
				'obscene' 	=> $image['obscene'],
				'postTime'	=> $image['postTime'],
				'caption'	=> $image['caption'],
				);
		}
		
		$this->type = "index";
		
		if($_SESSION['thumbnail'] == 1){
			$this->type = "thumb";
		}
	}

	/**
	 * Creates the Prev Next links on the page
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
	 * @since 0.5.0
	 * 
	 * @param int $image The image that is to be removed
	 */
	function dequeue($image){
		$id = prepareSQL($image);
		
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
	 * @since 0.13.0
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
	 * @return bool True if image is small
	 */
	function checkSmall($id){
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

/**
 * User class
 * Contains all the information regarding a user
 * 
 * @since 0.10.0
 */
class User{
	public $db = null;
	
	public $username;  //String, username
	public $userID;  //Int, the user's id
	public $group;
	
	public $isUser = false; //registered user or just anonymous
	
	public $permissions = array(); //Array(bool), contains user permissions
	
	/**
	 * Sets up the permissions array, checks if user is logged in, etc
	 * Starts with default values, and then fills in where appropriate
	 * @since 0.10.0
	 */
	function __construct(){
		global $db;
		$this->db = $db;
		
		$this->loggedIn();
		
		$this->permissions = array(
				"isAdmin" => 0,
				"toQueue" => 1
				);
		
		$sql = "SELECT * FROM users WHERE id = " . $this->userID . " LIMIT 1";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
		$this->permissions['isAdmin'] = $row['admin'];
		$this->group = $row['group'];
		
		$this->loadPermissions();
		
		$this->checkUploadLimit();
	}
	
	/**
	 * Loads user permissions
	 * @since 0.12.0
	 */
	function loadPermissions(){
		$sql = "SELECT * FROM groups WHERE id = " . $this->group . " LIMIT 1";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
		foreach($row as $key => $permission){
			if($key == "id" || $key == "name")
				continue;
			
			$this->permissions[$key] = $permission;
		}
	}
	
	/**
	 * Checks to see if a user is logged in
	 * Moved from lncln as of 0.10.0
	 * @since 0.10.0
	 * 
	 * @todo Move to a session based system as well, not just relying on cookies
     */
    function loggedIn(){
        if(isset($_COOKIE['password']) && isset($_COOKIE['username'])){
            $username = prepareSQL($_COOKIE['username']);
            $password = prepareSQL($_COOKIE['password']);
            
            $this->isUser = true;
        }
        else{
            $username = "Anonymous";
            $password = "";
            
            $this->isUser = false;
        }
    
        $sql = "SELECT id, name FROM users WHERE name = '" . $username . "' AND password = '" . $password . "'";
        $this->db->query($sql);

        $row = $this->db->fetch_one();
        
        $this->userID = $row['id'];
        $this->username = $row['name'];

        //removes any cookies that may have been set.
        if(!isset($_COOKIE['password']) && $_COOKIE['username']){
            setcookie("username", "", time() - (60 * 60 * 24));
            setcookie("password", "", time() - (60 * 60 * 24));
            header("location:". URL . "index.php");
        }
    }
	
	/**
	 * Checks if a user can upload straight to the homepage
	 * @since 0.10.0
	 * 
	 * @param bool $new If a user uploaded a new image, defaults to 0
	 */
	 function checkUploadLimit($new = 0){
	 	if($new == 1){
			$sql = "UPDATE users " .
					"SET postTime = " . time() . ", numImages = numImages + 1, uploadCount = uploadCount + 1 " .
					"WHERE id = '" . $this->userID . "' " .
					"LIMIT 1"; 
			$this->db->query($sql);
	 	}
	 	
	 	$sql = "SELECT postTime, numImages FROM users WHERE id = " . $this->userID;
	 	$result = mysql_query($sql);
	 	$row = mysql_fetch_assoc($result);
	 	
	 	//Number images <= group limit goto homepage, if 0 unlimited
	 	if($row['numImages'] <= $this->permissions['numIndex'] || ($this->permissions['index'] == 1 && $this->permissions['numIndex'] == 0)){
	 		$this->permissions['toQueue'] = 0;
	 	}
	 	else{
	 		$this->permissions['toQueue'] = 1;
	 	}
	 	
	 	//If over 24 hrs later, reset number images
	 	if(date('d', $row['postTime']) != date('d', time())){
	 		$sql = "UPDATE users SET postTime = " . time() . ", numImages = " . $new . " WHERE id = '" . $this->userID . "' LIMIT 1"; 
			$this->db->query($sql);
			
			$this->permissions['toQueue'] = 0;
	 	}
	 	
	 	if($this->permissions['index'] == 0){
	 		$this->permissions['toQueue'] = 1;
	 	}
	 }
	 
	/**
	 * Updates a user's information.
	 * Moved from lncln main class
	 * @since 0.5.0
	 * 
	 * @param array $user Contains the user's updated information
	 * 
	 * @return string Whether it updated or not
	 */
	function updateUser($user){
		$username = prepareSQL($user['username']);
		$obscene = prepareSQL($user['obscene']);
		
		if($user['password'] != "" && $user['newPassword'] != "" && $user['newPasswordConfirm'] != ""){
			$oldPassword = prepareSQL($user['password']);
			$newPassword = prepareSQL($user['newPassword']);
			$newPasswordConfirm = prepareSQL($user['newPasswordConfirm']);
			
			$sql = "SELECT password FROM users WHERE name = '" . $username . "' LIMIT 1";
			$this->db->query($sql);
			
			$row = $this->db->fetch_one();
			
			$oldPassword = sha1($oldPassword);
			$newPassword = sha1($newPassword);
			$newPasswordConfirm = sha1($newPasswordConfirm);
			
			if($newPassword != $newPasswordConfirm || $oldPassword != $row['password']){
				return "Passwords do not match";
			}
			
			$password = "password = '" . $newPassword . "',";
			
			setcookie("password", $newPassword, time() + (60 * 60 * 24));
		}
		
		$sql = "UPDATE users SET " . $password . " obscene = " . $obscene . " WHERE name = '" . $username . "' LIMIT 1";
		$this->db->query($sql);
		
		setcookie('obscene', $obscene, time() + (60 * 60 * 24), URL);
	
		
		return "User " . $username . " updated.";
	}
}

/**
 * Display class.  Manages settings
 * 
 * @since 0.11.0
 */
class Display{
	public $db = null;
	
	public $settings = array();
	public $lncln;
	
	public $rows;
	public $title;
	
	/**
	 * Sets up the settings array
	 * @since 0.11.0
	 */
	function __construct(&$lncln){
		global $db;
		$this->db = $db;
		
		$this->lncln = $lncln;
		
		$sql = "SELECT * FROM settings";
		$this->db->query($sql);
		$results = $this->db->fetch_all();
				
		foreach($results as $row){
			$this->settings[$row['name']] = $row['value'];
		}
		
		define("THEME", $this->settings['theme']);
		
		$this->set_title();
	}
	
	/**
	 * includes a file from include folder
	 * So that you can include files that require a $lncln variable from a module
	 * @since 0.13.0
	 */
	function includeFile($includeFile){
		$lncln = $this->lncln;
		
		include_once(ABSPATH . "includes/" . $includeFile);
	}
	
	/**
	 * Includes module related css
	 * @since 0.13.0
	 */
	function include_css(){
		$output = "";
		
		foreach($this->lncln->modules as $module){
			$name = strtolower($module->name);
			$file = "modules/" . $name . "/" . $name . ".css";
			
			if(file_exists(ABSPATH . $file)){
				$output .= "<link type='text/css' rel='stylesheet' href='" . URL . $file . "' />\n";
			}
		}
		
		return $output;
	}
	
	function set_title($title = ""){
		if($title == ""){
			$this->title = $this->settings['title'];
			return 1;
		}
		
		$this->title = $title . " - " . $this->settings['title'];
	}

	
	/**
	 * Show only the message given on the screen.
	 * Useful for "Please login"  or "Not allowed"
	 * Exits upon completion
	 * @since 0.12.0
	 * 
	 * @param $msg String Message to be shown
	 */
	function message($msg){
		$lncln = $this->lncln;
		
		include_once(ABSPATH . "includes/header.php");
		
		echo $msg;
		
		include_once(ABSPATH . "includes/footer.php");
		exit();
	}
}

/**
 * Prepares a variable for SQL
 * @since 0.9.0
 * 
 * @param string $var the variable that is being prepared
 * 
 * @return string Variable that's ready for SQL
 */
function prepareSQL($var){
	$var = mysql_real_escape_string($var);
	
	return $var;
}

/**
 * Creates a temporary name for uploads, returns a string
 * that is 25 random characters, a-zA-Z0-9
 * @since 0.10.0
 * 
 * @param string $name The name of the file that was uploaded, so it can pull the type
 * 
 * @return string 25 characters to use as a name for storing the temporary image
 */
function tempName($name){
	$typeTmp = split("\.", $name);
	$type = $typeTmp[count($typeTmp) - 1];
	
	$array = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
				   'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
				   'w', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7',
				   '8', '9', '0', 'A', 'B', 'c', 'D', 'E', 'F', 'G', 'H',
				   'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S',
				   'T', 'U', 'V', 'w', 'X', 'Y', 'Z'
				  );
	$string = "";
	
	for($i = 0; $i < 25; $i++){
	        $string .= $array[rand(0, count($array))];
	}
	
	return $string . '.' . $type;
}

/**
 * Creates an input based on type and other values
 * @since 0.13.0
 * 
 * @param $input array Keys: name, type, value
 * @param $id mixed Image that is being edited, id or temporary name
 * 
 * @return string Input string for form
 */
function createInput($input, $id, $extra = ""){
	switch($input['type']){
		case "text":
			return "<input type='text' name='images[$id][" . $input['name'] . "]' value='" . $input['value'] . "' " . $extra . " />";
		case "textarea":
			return "<textarea name='images[$id][" . $input['name'] . "]' " . $extra . " rows='10' cols='50'>" . $input['value'] . "</textarea>";
		case "select":
			$output = "<select name='images[$id][" . $input['name'] . "]' " . $extra . ">";
			
			foreach($input['options'] as $option){
				$selected = $option['name'] == $input['value'] ? "selected" : "";
				
				$output .= "<option value='" . $option['id'] . "' $selected>" . $option['name'] . "</option>";
			}
			
			$output .= "</select>";
			
			return $output;
	}
}

/**
 * Creates a form based on the array given
 * @since 0.13.0
 */
function create_form($form){
	$output = "";
	
	if($form['file'] == true){
		$file = " enctype='multipart/form-data' ";
	}
	
	$output .= "<form action='" . URL . $form['action'] . "' method='" . $form['method'] ."' $file>\n";
	$output .= "\t<div>\n";
	$output .= "\t\t<table>\n";
	
	foreach($form['inputs'] as $input){
		$output .= "\t\t\t<tr>\n";
		
		if($input['title'] != ""){
			$output .= "\t\t\t\t<td>" . $input['title'] . "</td>\n";	
		}
		
		switch($input['type']){
			case 'text':
				$output .= "\t\t\t\t<td><input type='text' name='" . $input['name'] . "' value='" . $input['value'] . "'/></td>\n";
				break;
			case 'hidden':
				$output .= "\t\t\t\t<td colspan='2'><input type='hidden' name='" . $input['name'] ."' value='" . $input['value'] . "'></td>\n";
				break;
		}
		
		$output .= "\t\t\t</tr>\n";
	}
	
	$output .= "\t\t</table>\n";
	$output .= "\t\t<input type='submit' value='" . $form['submit'] . "'/>\n";
	$output .= "\t</div>\n";
	$output .= "</form>\n";
	
	return $output;
}

/**
 * Easy access to the $db object
 * @since 0.13.0
 */
function get_db(){
	global $db;
	
	return $db;
}