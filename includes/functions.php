<?
/**
 * functions.php
 * 
 * One of the most important files, should be included in every page
 * Contains the main class lncln, as well as other functions
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0  $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
 
/**
 * The main class for lncln.  Shall handle most features.  
 * 
 * @since 0.6.0
 * @package lncln
 * 
 * @param string $action The action you wish to perform
 * @param array $params The parameters to be sent to the action
 */
 
class lncln{
	public $user;
	
	private $script;
	
	private $firstImage; 		//First image on the page (used to be $start)
	private $lastImage;			//Last image on the page 
	
	private $aboveFifty; 		//The image 50 images before it (used to be $prev)
	private $belowFifty;		//The image 50 images after it ($used to be $next)
	
	private $highestID; 		//The highest ID in the database ($used to be $numImgs)
	private $lowestID;
	
	private $search; 			//tag being searched for
	private $album;				//album being viewed
	private $queue = false;		//if you're in the queue
	private $uploaded = array();
	
	private $imagesToGet = array();		//The images that data will be pulled for
	private $images = array(); 	//Image data to be outputed in listImages.php
	private $type;				//Normal or thumb
	private $extra;				//If $type == "thumb" then it equals "&thumb=true"
	
	/**
	 * Gets the class ready for action!
	 * 
	 * @since 0.6.0
	 * @package lncln
	 * 
	 * @param string $action Which type of page is being loaded
	 * @param array $params any extra parameters that will be passed onto the action
	 */
	function __construct($action = "none", $params = array()){	
		$this->user = new User();
		
		if($action != "none"){
			if(method_exists($this, $action)){
				$this->$action($params);
			}
		}
		
		$this->script = split("/", $_SERVER['SCRIPT_NAME']);
		$this->script = $this->script[count($this->script) - 1];
	}
	
	/**
	 * The function that makes the index go round
	 * 
	 * @since 0.9.0
	 * @package lncln
	 */
	private function index(){
		$time = !$this->user->permissions['isAdmin'] ? " AND postTime <= " . time() . " " : "";
		
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 0 " . $time;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['COUNT(*)'] == 0){
			$this->aboveFifty = 0;
			$this->belowFifty = 0;
			$this->firstImage = 0;
			$this->highestID = 0;
		}
		else{
			$result = mysql_query("SELECT MAX(id) FROM images WHERE queue = 0 " . $time);
			$result = mysql_fetch_assoc($result);

			$this->highestID = $result['MAX(id)'];
			
			$sql = "SELECT MIN(id) FROM images WHERE queue = 0 " . $time;
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			$this->lowestID = $row['MIN(id)'];
			
			if(!isset($_GET['img'])){
				$this->firstImage = $this->highestID;
			}
			else{
				//if it's set, then set it to start
				$this->firstImage = $_GET['img'];
				if($this->firstImage == ""){
					$this->firstImage = $this->highestID;
				}
				//incase its to large
				if($this->firstImage > $this->highestID){
					$this->firstImage = $this->highestID;
				}
			}
		
			//Getting the number to start the next page && the ids that the page needs to load.
			$sql = "SELECT id FROM `images` WHERE id <= " . $this->firstImage . " AND queue = 0 " . $time. " ORDER BY id DESC LIMIT 51";
			$result = mysql_query($sql);
			
			$numRows = mysql_num_rows($result);
			
			for($i = 0; $i < $numRows; $i++){
				$row = mysql_fetch_assoc($result);
				
				if ($i == $numRows - 1){
					$this->belowFifty = $row['id'];
				}
				
				$this->imagesToGet[] = $row['id'];
			}

			if(count($this->imagesToGet) > 50){
				array_pop($this->imagesToGet);
			}
			
			$this->lastImage = $this->imagesToGet[count($this->imagesToGet) - 1];
			
			//getting the prevsion page
			$sql = "SELECT id FROM `images` WHERE id > " . $this->firstImage . " AND queue = 0 " . $time. " ORDER BY id ASC LIMIT 50";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			$numRows = mysql_num_rows($result);
            if($numRows > 0){	
				mysql_data_seek($result, $numRows - 1);
                $row = mysql_fetch_assoc($result);    
                $this->aboveFifty = $row['id'];
			}
			else{
				$this->aboveFifty = $this->firstImage;
			}
		}
	}
	
	/**
	 * Function that makes image.php go round
	 * 
	 * @since 0.9.0
	 * @package lncln
	 */
	private function image(){
		if(isset($_GET['img']) && is_numeric($_GET['img'])){
			$image = prepareSQL($_GET['img']);
		}
		else{
			return;
		}

		$this->imagesToGet[] = $image;
				
		$this->aboveFifty = 0;
		$this->belowFifty = 0;
		$this->firstImage = $image;
		$this->highestID = $image;
	}
	
	/**
	 * Makes searching happen
	 * 
	 * @since 0.9.0
	 * @package lncln
	 * 
	 * @param array $search The first term of the array is the search term
	 */
	private function search($search){
		$this->search = prepareSQL($search[0]);
		
		$sql = "SELECT COUNT(*) FROM tags WHERE tag LIKE '%" . $this->search . "%'";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['COUNT(*)'] == 0){
			$this->aboveFifty = 0;
			$this->belowFifty = 0;
			$this->firstImage = 0;
			$this->highestID = 0;
		}
		else{		
			$sql = "SELECT MAX(picId) FROM tags WHERE tag LIKE '%" . $this->search . "%'";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			$this->highestID = $row['MAX(picId)'];
			
			$sql = "SELECT MIN(picId) FROM tags WHERE tag LIKE '%" . $this->search . "%'";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			$this->lowestID = $row['MIN(picId)'];
			
			if(isset($search[1]) && is_numeric($search[1]) && $search[1] != ""){
				$id = " AND picId <= " . prepareSQL($search[1]);
			}
			else{
				$id = "";
			}
			
			$sql = "SELECT picId FROM tags WHERE tag LIKE '%" . $this->search . "%' " . $id . " ORDER BY picId DESC LIMIT 51";
			$result = mysql_query($sql);
	
			while($row = mysql_fetch_assoc($result)){
				$this->imagesToGet[] = $row['picId'];
			}
					
			$this->belowFifty = $this->imagesToGet[count($this->imagesToGet) - 1];
			
			if(count($this->imagesToGet) > 50){
				array_pop($this->imagesToGet);
			}
			
			$this->firstImage = $this->imagesToGet[0];
			$this->lastImage = $this->imagesToGet[count($this->imagesToGet) - 1];
			
			$sql = "SELECT picId FROM tags WHERE tag LIKE '%" . $this->search . "%' AND picId > " . $this->firstImage . " ORDER BY picId ASC LIMIT 50";
			$result = mysql_query($sql);
			
			$numRows = mysql_num_rows($result);
			if($numRows > 0){
				mysql_data_seek($result, $numRows - 1);
				$row = mysql_fetch_assoc($result);	
				$this->aboveFifty = $row['picId'];
			}
			else{
				$this->aboveFifty = $this->firstImage;
			}
			
			$this->extra .= "&amp;search=" . $this->search;
		}
	}
	
	/**
	 * Function for loading albums
	 * 
	 * @since 0.9.0
	 * @package lncln
	 * 
	 * @param array $album First term is which album to load
	 */
	private function album($album){	
		if($album[0] != 0){
			$this->album = prepareSQL($album[0]);
			$time = !$this->user->permissions['isAdmin'] ? " AND postTime <= " . time() . " " : "";
			
			$sql = "SELECT COUNT(*) FROM images WHERE queue = 0 AND album = " . $this->album . $time;
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			if($row['COUNT(*)'] == 0){
				$this->aboveFifty = 0;
				$this->belowFifty = 0;
				$this->firstImage = 0;
				$this->highestID = 0;
			}
			else{				
				$sql = "SELECT MAX(id) FROM images WHERE album = " . $this->album . $time;
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				
				$this->highestID = $row['MAX(id)'];
				
				$sql = "SELECT MIN(id) FROM images WHERE album = " . $this->album . $time;
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				
				$this->lowestID = $row['MIN(id)'];
				
				if(isset($album[1]) && is_numeric($album[1]) && $album[1] != ""){
					$id = " AND id <= " . prepareSQL($album[1]);
				}
				else{
					$id = "";
				}
				
				$sql = "SELECT id FROM images WHERE album = " . $this->album . " " . $id . " AND queue = 0 " . $time. " ORDER BY id DESC LIMIT 51";
				$result = mysql_query($sql);
		
				while($row = mysql_fetch_assoc($result)){
					$this->imagesToGet[] = $row['id'];
				}
						
				$this->belowFifty = $this->imagesToGet[count($this->imagesToGet) - 1];
				
				if(count($this->imagesToGet) > 50){
					array_pop($this->imagesToGet);
				}
				
				$this->firstImage = $this->imagesToGet[0];
				$this->lastImage = $this->imagesToGet[count($this->imagesToGet) - 1];
				
				$sql = "SELECT id FROM images WHERE album = " . $this->album . " AND id > " . $this->firstImage . " AND queue = 0 " . $time. " ORDER BY id ASC LIMIT 50";
				$result = mysql_query($sql);
				
				$numRows = mysql_num_rows($result);
				if($numRows > 0){
					mysql_data_seek($result, $numRows - 1);
					$row = mysql_fetch_assoc($result);	
					$this->aboveFifty = $row['id'];
				}
				else{
					$this->aboveFifty = $this->firstImage;
				}
				
				$this->extra .= "&amp;album=" . $this->album;
			}
		}
	}
	
	/**
	 * Function for loading the queue
	 * 
	 * @since 0.9.0
	 * @package lncln
	 */
	function queue(){
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['COUNT(*)'] > 0){
			$sql = "SELECT id FROM images WHERE queue = 1 ORDER BY `id` ASC LIMIT 50";
			$result = mysql_query($sql);
	
			while($row = mysql_fetch_assoc($result)){
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
	 * 
	 * @since 0.9.0
	 * @package lncln
	 * 
	 * @param array $rss First term is the type of rss feed (all/safe)
	 */
	function rss($rss){
		$safe = $rss[0] == "safe" ? " AND obscene = 0" : "";
		
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 0 " . $safe;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['COUNT(*)'] > 0){
			$sql = "SELECT id FROM images WHERE queue = 0 AND postTime <= " . time() . " " . $safe . " ORDER BY `id` DESC LIMIT 50";
			$result = mysql_query($sql);
			
			while($row = mysql_fetch_assoc($result)){
				$this->imagesToGet[] = $row['id'];
			}
		}
	}
	
	/**
	 * Limits the availability of certain variables
	 * 
	 * @since 0.6.0
	 * @package lncln
	 * 
	 * @param mixed $variable The variable to be returned
	 * 
	 * @return mixed The variable in question
	 */
	function __get($variable){
		return $this->$variable;
	}
	
	function __set($variable, $value){
		$this->$variable = $value;
	}
	
	/**
	 * Creates thumbnails for the site.  Uses ImageMagick.  For gifs
	 * it has to do a temporary jpeg and then back to gif.
	 * 
	 * Should really make it so that if ImageMagick isn't installed
	 * it doesn't die.
	 * 
	 * @since 0.5.0
	 * @package lncln
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
	 * 
	 * @todo possibly rename to getData()
	 * 
	 * @since 0.5.0
	 * @package lncln 
	 */
	function img(){		
		$time = $this->isAdmin != true ? " AND postTime <= " . time() : "";
		
		$sql = "SELECT id, caption, postTime, type, album, obscene, rating FROM images WHERE ";
		
		foreach($this->imagesToGet as $image){
			$sql .= " id = " . $image . " OR ";
		}
		
		if(count($this->imagesToGet) > 0)
			$sql = substr_replace($sql, "", -4);
		$sql .= $time;
		$sql .= " ORDER BY `id` DESC";
		
		$result = mysql_query($sql);
		$numRows = @mysql_num_rows($result);
		
		for($i = 0; $i < $numRows; $i++){
			$image = mysql_fetch_assoc($result);
			
			$sql = "SELECT tag FROM tags WHERE picId = " . $image['id'];
			$tags = mysql_query($sql);
			
			$imageTags = array();
			
			while($tag = mysql_fetch_assoc($tags)){
				$imageTags[] = $tag['tag'];
			}
			
			if($image['album'] != 0){
				$sql = "SELECT name FROM albums WHERE id = " . $image['album'];
				$album = mysql_query($sql);
				$album = mysql_fetch_assoc($album);
			}
			else{
				$album['name'] = "No Album";
			}
			
			
			$this->images[$i] = array(
				'id' 		=> $image['id'],
				'file' 		=> $image['id'] . "." . $image['type'],
				'type'		=> $image['type'],
				'album'		=> $album['name'],
				'obscene' 	=> $image['obscene'],
				'rating' 	=> $image['rating'],
				'postTime'	=> $image['postTime'],
				'caption'	=> $image['caption'],
				'tags' 		=> $imageTags
				);
		}
		
		$this->type .= "index";
		$this->extra .= "";
		
		if($_GET['thumb']){
			$this->type = "thumb";
			$this->extra .= "&amp;thumb=true";
		}
	}

	/**
	 * Creates the Prev Next links on the page
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @return string Contains the links Prev Next
	 */
	function prevNext(){
		$extra = $this->type == "thumb" ? "&amp;thumb=true" : "";
		$extra .= $this->search == "" ? "" : "&amp;search=" . $this->search;
		$extra .= $this->album == "" ? "" : "&amp;album=" . $this->album;
		
		if ($this->firstImage == $this->highestID && $this->lastImage != $this->lowestID){
	        return "<a href='" . $this->script . "?img=" . $this->belowFifty . $extra . "' class='prevNext'>Next 50</a>";
	    }
	    elseif($this->firstImage == $this->highestID && $this->lastImage == $this->lowestID){
	    	return "";
	    }
	    elseif($this->lastImage == $this->lowestID){
	        return "<a href='" . $this->script . "?img=" . $this->aboveFifty . $extra . "' class='prevNext'>Prev 50</a>";
	    }
	    else{
	        return "<a href='" . $this->script . "?img=" . $this->aboveFifty . $extra . "' class='prevNext'>Prev 50</a>
	        <a href='" . $this->script . "?img=" . $this->belowFifty . $extra . "' class='prevNext'>Next 50</a>";
	    }
	}

	/**
	 * The first part of uploading, moves the image to a temporary spot
	 * This allows for taging, captions, etc
	 * 
	 * @since 0.10.0
	 * @package lncln
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
			$typeTmp = $_GET['URL'] ? split("\.", $_POST['upload' . $i]) : split("\.", $_FILES['upload'.$i]['name']);
			
	        //the file extension
			$type = $typeTmp[count($typeTmp) - 1];
			
			//only these types
			if($type == "png" || $type == "jpg" || $type == "gif"){
				if($_GET['url']){
					$file = @file_get_contents($_POST['upload' . $i]);
					if(!$file){
						$_SESSION['upload'][$i] = 5;
						continue;
					}
				}
				
				$name = tempName($_FILES['upload' . $i]['name']);
				
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
	 * 
	 * @since 0.5.0
	 * @package lncln
	 */
	function upload($name, $data){
		$sql = "SELECT MAX(postTime) FROM images";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
					
		$postTime = time() >= ($row['MAX(postTime)'] + (60 * 15)) ? time() : $row['MAX(postTime)'] + (60 * 15);

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
		
		mysql_query($sql);
		
		$imgID = str_pad(mysql_insert_id(), 6, 0, STR_PAD_LEFT);
				
		$_SESSION['image'][$_SESSION['uploadKey'][$name]] = $imgID . '.' . $type;
		
		rename(CURRENT_IMG_TEMP_DIRECTORY . $name, CURRENT_IMG_DIRECTORY . $imgID . '.' . $type);
		
		$this->thumbnail($imgID . '.' . $type);
		$this->tag($imgID, $data['tags']);
		$this->caption($imgID, $data['caption']);
		$this->changeAlbum($imgID, $data['album']);
		if($data['obscene']){
			$this->obscene($imgID);
		}
	}
	
	/**
	 * Removes an image from the queue
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @param int $image The image that is to be removed
	 */
	function dequeue($images){
		//This is line #666, watch out
		foreach($images as $image){
			$sql = "UPDATE images SET queue = 0, report = 0 WHERE id = " . $image . " LIMIT 1";
			mysql_query($sql);
		}
	}

	/**
	 * Adds a user to the site.
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @param array $user Contains the users information, username, password, if they're an admin
	 * 
	 * @return string If bad password, or if they were added successfully
	 */
	function adduser($user){
		$username = stripslashes($user['username']);
		$password = stripslashes($user['password']);
		$passwordConfirm = stripslashes($user['passwordconfirm']);
		$admin = stripslashes($user['admin']);
	
		$username = mysql_real_escape_string($username);
		$password = mysql_real_escape_string($password);
		$passwordConfirm = mysql_real_escape_string($passwordConfirm);
		$admin = mysql_real_escape_string($admin);
		
		$password = sha1($password);
		$passwordConfirm = sha1($passwordConfirm);
		
		if($password != $passwordConfirm){
			return "Passwords do not match";
		}
		
		$sql = "INSERT INTO users (name, password, admin) VALUES ('" . $username . "', '" . $password . "', " . $admin . ")";
		mysql_query($sql);
		
		return "User " . $username . " added";
	}
	
	/**
	 * Updates a user's information.
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @param array $user Contains the user's updated information
	 * 
	 * @return string Whether it updated or not
	 */
	function updateUser($user){
		$username = stripslashes($user['username']);
		$obscene = stripslashes($user['obscene']);
	
		$username = mysql_real_escape_string($username);
		$obscene = mysql_real_escape_string($obscene);
		
		if($user['password'] != "" && $user['newPassword'] != "" && $user['newPasswordConfirm'] != ""){
			$oldPassword = stripslashes($user['password']);
			$newPassword = stripslashes($user['newPassword']);
			$newPasswordConfirm = stripslashes($user['newPasswordConfirm']);
			
			$oldPassword = mysql_real_escape_string($oldPassword);
			$newPassword = mysql_real_escape_string($newPassword);
			$newPasswordConfirm = mysql_real_escape_string($newPasswordConfirm);
			
			$sql = "SELECT password FROM users WHERE name = '" . $username . "' LIMIT 1";
			$result = mysql_query($sql);
			
			$row = mysql_fetch_assoc($result);
			
			$oldPassword = sha1($oldPassword);
			$newPassword = sha1($newPassword);
			$newPasswordConfirm = sha1($newPasswordConfirm);
			
			if($newPassword != $newPasswordConfirm || $oldPassword != $row['password']){
				return "Passwords do not match";
			}
			
			$password = "password = '" . $newPassword . "',";
			
			setcookie("password", $newPassword, time() + (60 * 60 * 24));
		}
		
		$obscene = $_POST['viewObscene'] ? 1 : 0;
		
		$sql = "UPDATE users SET " . $password . " obscene = " . $obscene . " WHERE name = '" . $username . "' LIMIT 1";
		mysql_query($sql);
		
		setcookie("username", $username, time() + (60 * 60 * 24));
		setcookie('obscene', $obscene, time() + (60 * 60 * 24));
	
		
		return "User " . $username . " updated";
	}
	
	/**
	 * Removes an image.  First deletes the image from sql and then unlinks
	 * the image itself and then the two thumbnails
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @param int $image The image to be deleted
	 * 
	 * @return string Whether it deleted it or not
	 */
	function delete($image){
		$sql = "SELECT type FROM images WHERE id = " . $image . " LIMIT 1";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) == 1){
			$type = mysql_fetch_assoc($result);
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
	 * Obscenes images.  Just flips the images obscene number
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @param int $image The image to be changed
	 * 
	 * @return string If it change the image or not.
	 */
	function obscene($image){
		$sql = "SELECT type, obscene FROM images WHERE id = " . $image;
		
		$result = mysql_query($sql);
		if(mysql_num_rows($result) == 1){
			$row = mysql_fetch_assoc($result);
			$num = $row['obscene'] == 0 ? 1 : 0;
		}
		else{
			return "No such image.";
		}
		
		$sql = "UPDATE images SET obscene = " . $num . " WHERE id = " . $image;
		
		mysql_query($sql);
		
		return "Updated image";
	}
	
	/**
	 * Rates an image.  Adds the user to a table named rating with
	 * their up or down.
	 * 
	 * @todo Need to come back and escape $image, $user, and $rating.
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @todo Delete record if they already voted.
	 * 
	 * @param int $image The image to be changed
	 * @param int $user The user that is doing the rating
	 * @param int $rating The rating, could be -1, 1, -5, or 5
	 * 
	 * @return string Whether rating went swell or not
	 */
	function rate($image, $rating){
		$sql = "SELECT upDown FROM rating WHERE picId = " . $image . " AND userId = " . $this->userID;
		$result = mysql_query($sql);
		$numRows = mysql_num_rows($result);
		
		if($numRows > 0){
			$row = mysql_fetch_assoc($result);
		}
		
		if($numRows == 1 && $row['upDown'] == $rating){
			return "You already rated it";
		}
		elseif(($numRows == 1 && $row['upDown'] != $rating) || $numRows == 0){
			if(isset($row['upDown']) && $row['upDown'] != $rating){
				$sql = "DELETE FROM rating WHERE picID = " . $image . " AND userID = " . $this->userID;
			}
			else{
				$sql = "INSERT INTO rating (picID, userId, upDown) VALUES (" . $image . ", " . $this->userID . ", " . $rating . ")";
			}
			
			mysql_query($sql);
			
			$sql = "SELECT SUM(upDown) FROM rating WHERE picId = " . $image;
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			if($row['SUM(upDown)'] == null){
				$row['SUM(upDown)'] = 0;
			}
			
			$sql = "UPDATE images SET rating = " . $row['SUM(upDown)'] . " WHERE id = " . $image . " LIMIT 1";
			mysql_query($sql);
			
			return "Rated successfully";
		}
		elseif($numRows > 0){
			return "You already rated it";
		}
	}

	/**
	 * Adds a caption to a picture
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @param int $id The id of the image to have a caption added
	 * @param string $caption The caption for the image
	 */
	function caption($id, $caption){
		$id = stripslashes($id);
		$caption = stripslashes($caption);
	
		$id = mysql_real_escape_string($id);
		$caption = mysql_real_escape_string($caption);
		
		$sql = "UPDATE images SET caption = '" . $caption . "' WHERE id = " . $id . " LIMIT 1";
		mysql_query($sql);
	}
	
	/**
	 * Tags an image.  Splits the $tags string by ','s and then secures it
	 * for MySQL insertion.  
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @param int $id The id of the image
	 * @param string $tags A comma seperated string that contains the tags
	 */
	function tag($id, $tags){
		$id = stripslashes($id);
		$id = mysql_real_escape_string($id);
		
		$tags = split(',', $tags);
		$tags = array_map('trim', $tags);
		$tags = array_map('stripslashes', $tags);
		$tags = array_map('mysql_real_escape_string', $tags);
		
		$sql = "DELETE FROM tags WHERE picId = " . $id;
		mysql_query($sql);
		
		$sql = "INSERT INTO tags (picId, tag) VALUES ";
		
		foreach($tags as $tag){
			if($tag == ""){
				continue;
			}
			$sql .= "(" . $id . ", '" . $tag . "'), ";
		}
	
		$sql = substr_replace($sql ,"",-2);
		
		mysql_query($sql);
	}
	
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
	 * Returns all of the albums currently in the database
	 * 
	 * @since 0.9.0
	 * @package lncln
	 * 
	 * @return array All of the albums in their own arrays, with 'id' and 'name'
	 */
	function getAlbums(){
		$sql = "SELECT id, name FROM albums WHERE 1";
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_assoc($result)){
			$albums[] = array("id"	 => $row['id'],
							  "name" => $row['name']
							  );	
		}
		
		return $albums;
	}
	
	/**
	 * Changes an image's album
	 * 
	 * @since 0.9.0
	 * @package lncln
	 * 
	 * @param int $img The image to be changed
	 * @param int $album The album id 
	 */
	function changeAlbum($img, $album){
		$img = prepareSQL($img);
		$album = prepareSQL($album);
		
		$sql = "UPDATE images SET album = " . $album . " WHERE id = " . $img;
		mysql_query($sql);
	}
	
	/**
	 * Debug function to print out the private variables;
	 * 
	 * @since 0.9.0
	 * @package lncln
	 */
	function debug(){
		echo "isAdmin: " . $this->isAdmin . "\n";
		echo "isLoggedIn: " . $this->isLoggedIn . "\n";
		echo "userID: " . $this->userID . "\n";
		
		echo "script: " . $this->script . "\n";
		
		echo "firstImage: " . $this->firstImage . "\n";
		echo "lastImage: " . $this->lastImage . "\n";
		
		echo "aboveFifty: " . $this->aboveFifty . "\n";
		echo "belowFifty: " . $this->belowFifty . "\n";
		
		echo "highestID: " . $this->highestID . "\n";
		echo "lowestID: " . $this->lowestID . "\n";
		
		echo "search: " . $this->search . "\n";
		echo "album: " . $this->album . "\n";
		echo "queue: " . $this->queue . "\n";
		
		echo "imagesToGet: ";
		print_r($this->imagesToGet);
		
		echo "images: ";
		print_r($this->images);
		echo "type: " . $this->type . "\n";
		echo "extra: " . $this->extra . "\n";
	}
}

/**
 * User class
 * Contains all the information regarding a user
 * 
 * @since 0.10.0
 * @package lncln
 */
class User{
	public $username;  //String, username
	public $userID;  //Int, the user's id
	
	public $isUser = false; //registered user or just anonymous
	
	public $permissions = array(); //Array(bool), contains user permissions
	
	/**
	 * Sets up the permissions array, checks if user is logged in, etc
	 * Starts with default values, and then fills in where appropriate
	 * 
	 * @since 0.10.0
	 * @package lncln
	 */
	function __construct(){
		$this->loggedIn();
		
		$this->permissions = array(
				"isAdmin" => 0,
				"toQueue" => 1
				);
		
		$sql = "SELECT * FROM users WHERE id = " . $this->userID . " LIMIT 1";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		$this->permissions['isAdmin'] = $row['admin'];
		$this->permissions['toHome'] = $row['toHome'];
		
		$this->checkUploadLimit();
	}
	
	/**
	 * Checks to see if a user is logged in
	 * Moved from lncln as of 0.10.0
	 * 
	 * @since 0.10.0
	 * @package lncln
	 * 
	 * @todo Move to a session based system as well, not just relying on cookies
	 */
	function loggedIn(){
		if(isset($_COOKIE['password']) && isset($_COOKIE['username'])){
			$username = stripslashes($_COOKIE['username']);
			$password = stripslashes($_COOKIE['password']);
	
			$username = mysql_real_escape_string($username);
			$password = mysql_real_escape_string($password);
			
			$this->isUser = true;
		}
		else{
			$username = "Anonymous";
			$password = "";
			
			$this->isUser = false;
		}
	
		$sql = "SELECT id, name FROM users WHERE name = '" . $username . "' AND password = '" . $password . "'";
		$result = mysql_query($sql);

		$row = mysql_fetch_assoc($result);
		
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
	 * 
	 * @since 0.10.0
	 * @package lncln
	 * 
	 * @param bool $new If a user uploaded a new image, defaults to 0
	 */
	 function checkUploadLimit($new = 0){
	 	if($new == 1){
			$sql = "UPDATE users SET postTime = " . time() . ", numImages = numImages + 1 WHERE id = '" . $this->userID . "' LIMIT 1"; 
			mysql_query($sql);
	 	}
	 	
	 	$sql = "SELECT postTime, numImages FROM users WHERE id = " . $this->userID;
	 	$result = mysql_query($sql);
	 	$row = mysql_fetch_assoc($result);
	 	
	 	//Number images <= 20 goto homepage
	 	if($row['numImages'] <= 20){
	 		$this->permissions['toQueue'] = 0;
	 	}
	 	
	 	//If over 24 hrs later, reset number images
	 	if(date('d', $row['postTime']) != date('d', time())){
	 		$sql = "UPDATE users SET postTime = " . time() . ", numImages = " . $new . " WHERE id = '" . $this->userID . "' LIMIT 1"; 
			mysql_query($sql);
			
			$this->permissions['toQueue'] = 0;
	 	}
	 	
	 	if($this->permissions['toHome'] == 0){
	 		$this->permissions['toQueue'] = 1;
	 	}
	 }
	 
	/**
	 * Updates a user's information.
	 * Moved from lncln main class
	 * 
	 * @since 0.5.0
	 * @package lncln
	 * 
	 * @param array $user Contains the user's updated information
	 * 
	 * @return string Whether it updated or not
	 */
	function updateUser($user){
		$username = stripslashes($user['username']);
		$obscene = stripslashes($user['obscene']);
	
		$username = mysql_real_escape_string($username);
		$obscene = mysql_real_escape_string($obscene);
		
		if($user['password'] != "" && $user['newPassword'] != "" && $user['newPasswordConfirm'] != ""){
			$oldPassword = stripslashes($user['password']);
			$newPassword = stripslashes($user['newPassword']);
			$newPasswordConfirm = stripslashes($user['newPasswordConfirm']);
			
			$oldPassword = mysql_real_escape_string($oldPassword);
			$newPassword = mysql_real_escape_string($newPassword);
			$newPasswordConfirm = mysql_real_escape_string($newPasswordConfirm);
			
			$sql = "SELECT password FROM users WHERE name = '" . $username . "' LIMIT 1";
			$result = mysql_query($sql);
			
			$row = mysql_fetch_assoc($result);
			
			$oldPassword = sha1($oldPassword);
			$newPassword = sha1($newPassword);
			$newPasswordConfirm = sha1($newPasswordConfirm);
			
			if($newPassword != $newPasswordConfirm || $oldPassword != $row['password']){
				return "Passwords do not match";
			}
			
			$password = "password = '" . $newPassword . "',";
			
			setcookie("password", $newPassword, time() + (60 * 60 * 24));
		}
		
		$obscene = $_POST['viewObscene'] ? 1 : 0;
		
		$sql = "UPDATE users SET " . $password . " obscene = " . $obscene . " WHERE name = '" . $username . "' LIMIT 1";
		mysql_query($sql);
		
		setcookie('obscene', $obscene, time() + (60 * 60 * 24));
	
		
		return "User " . $username . " updated";
	}
}

/**
 * Connects to the database
 * 
 * @since 0.5.0
 * @package lncln
 * 
 * @param array $config Contains the information needed to connect to the database
 */
function connect(){
	if(!@mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD)){
		die("Error with MySQL: " . mysql_error());
	}
	mysql_select_db(DB_DATABASE);
}

/**
 * Prepares a variable for SQL
 * 
 * @since 0.9.0
 * @package lncln
 * 
 * @param string $var the variable that is being prepared
 * 
 * @return string Variable that's ready for SQL
 */
function prepareSQL($var){
	$var = stripslashes($var);
	$var = mysql_real_escape_string($var);
	
	return $var;
}

/**
 * Creates a temporary name for uploads, returns a string
 * that is 25 random characters, a-zA-Z0-9
 * 
 * @since 0.10.0
 * @package lncln
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
?>