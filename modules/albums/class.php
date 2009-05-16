<?
/**
 * class.php
 * 
 * Albums module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

class Albums{
	public $name = "Albums";
	public $displayName = "Album";
	
	/**
	 * Construct to pass the reference of lncln so that modules 
	 * can access permissions and settings
	 * @since 0.13.0
	 * 
	 * @param $lncln lncln Main class variable
	 */
	public function __construct(&$lncln){
		$this->lncln = $lncln;
	}
	
	/**
	 * Called if the Module has it's own page
	 * Such as albums or search
	 * @since 0.13.0
	 */
	public function index(){
		$this->album();
		
		$this->lncln->display->includeFile("iconActions.php");
		
		$this->lncln->img();
		
		$this->lncln->display->includeFile("header.php");
		
		if(!isset($this->lncln->params[0]) || $this->lncln->params[0] == ""){
			$albums = $this->getAlbums(false);
			
			if(count($albums) == 0){
				echo "\t\t\tNo Albums";
			}
			
			foreach($albums as $album){
		?>
					<a href="<?=URL;?>albums/<?=$this->getAlbumName($album['id'], true);?>/1"><?=$album['name'];?></a><br />
		<?
			}
		}
		else{
			echo "You're viewing " . $this->lncln->params[0] . "\n <br />";
			
			echo $this->lncln->prevNext();
			
			$this->lncln->display->includeFile("listing.php");

			echo $this->lncln->prevNext(true);
		}
		
		$this->lncln->display->includeFile("footer.php");
	}
	
	/**
	 * Called after a successful upload
	 * @since 0.13.0
	 * 
	 * @param $id int ID of new image
	 * @param $data array Extra material needed, tag information, etc
	 */
	public function add($id, $data){
		$this->edit($id, $data);
	}
	
	/**
	 * Edits an image with the data provided
	 * @since 0.13.0
	 * 
	 * @param $id int ID of image
	 * @param $data array Extra material needed, tag information, etc
	 */	
	public function edit($id, $data){
		$img = prepareSQL($id);
		$album = prepareSQL($data[0]);
		
		$sql = "UPDATE images SET album = " . $album . " WHERE id = " . $id;
		mysql_query($sql);
	}
	
	/**
	 * Called during the upload screen. Contains the form information needed,
	 * will be passed to add() after successful upload
	 * @since 0.13.0
	 *
	 * @return array Keys: type, name, value, options
	 */
	public function upload(){
		return array("type" => "select", "name" => "albums", "value" => "", "options" => $this->getAlbums());
	}
	
	/**
	 * Creates the form information needed during moderation
	 * @since 0.13.0
	 * 
	 * @param $id int Image to gather information about and populate the input
	 *
	 * @return array Keys: type, name, value, options
	 */
	public function moderate($id){
		return array("type" => "select", "name" => "albums", "value" => $this->getImageAlbum($id), "options" => $this->getAlbums());
	}
	
	/**
	 * Creates the link in the header
	 * @since 0.13.0
	 *
	 * @return string Link or form
	 */
	public function header_link(){
		return "\t\t\t\t\t<a href='" . URL . "albums/'>Albums</a>\n";
	}
	
	/**
	 * Creates the icon underneath images
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return string Icon underneath the image
	 */
	public function icon($id){
		return "";
	}
	
	/**
	 * Creates text above the image.  Text only
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return string Text above the image
	 */
	public function above($id){
		return "";
	}
	
	/**
	 * Creates text underneath the image.  May contain a form
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return string Text underneath the image
	 */
	public function below($id){
		if($this->lncln->user->permissions['albums'] == 1){
			$class = "class='underImage'";
			$onClick = "onclick=\"showModule('" . $this->name . "', '" . $id . "');\"";
		}
		else{
			$class = "";
			$onClick = "";
		}
		
		$output = "			
			<div id='albums$id' " . $class . $onClick . ">
				Album: " . $this->getImageAlbum($id) . "
			</div>";
		
		
		if($this->lncln->user->permissions['albums'] == 1){
			$output .= "
			<form id='a$id' style='display: none;' action='" . URL . "action/albums/$id' method='post'>
				<div>
					<input type='hidden' name='id' value='$id' />
					<select name='albums' id='formAlbums$id'>";
			foreach($this->getAlbums() as $album){
				$selected = $album['name'] == $this->getImageAlbum($id) ? "selected" : "";
				$output .= "<option value='" . $album['id'] ."' $selected>" . $album['name'] . "</option>";
			}
			$output .= "
					</select>
					<input type='submit' value='Change album' />
				</div>
			</form>";
		}
		
		return $output;
	}
	
	/**
	 * Pushes content out via the RSS feed
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return string Output for the RSS feed
	 */
	public function rss($id){
		return "";
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Function for loading albums
	 * @since 0.9.0
	 */
	protected function album(){	
		$album = prepareSQL($this->lncln->params[0]);
		
		$album = $this->getAlbumId($album);
		
		if($album != 0){
			$time = !$this->lncln->user->permissions['isAdmin'] ? " AND postTime <= " . time() . " " : "";
			
			$sql = "SELECT COUNT(*) FROM images WHERE queue = 0 AND album = " . $album . $time;
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			if($row['COUNT(*)'] == 0){
				$this->lncln->page = 0;
			}
			else{				
				$sql = "SELECT COUNT(id) FROM images WHERE album = " . $album . $time;
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				
				$this->lncln->maxPage = ceil($row['COUNT(id)'] / $this->lncln->display->settings['perpage']);
				
				$page = (int)end($this->lncln->params);

				if(!isset($page)){
					$this->lncln->page = 1;
				}
				else{
					if(is_numeric($page)){
						$this->lncln->page = $page;	
					}
					else{
						$this->lncln->page = 1;
					}
				}
				
				$offset = ($this->lncln->page - 1) * $this->lncln->display->settings['perpage'];
				
				$sql = "SELECT id FROM images WHERE album = " . $album . " AND queue = 0 " . $time. " ORDER BY id DESC LIMIT " . $offset . ", " . $this->lncln->display->settings['perpage'];
				$result = mysql_query($sql);
		
				while($row = mysql_fetch_assoc($result)){
					$this->lncln->imagesToGet[] = $row['id'];
				}
			}
		}
	}
	
	/**
	 * Get an album name based on an image
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return String Album name
	 */
	protected function getImageAlbum($id){
		$sql = "SELECT album FROM images WHERE id = " . $id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['album'] == 0){
			return "No Album";
		}
		
		return $this->getAlbumName($row['album']);
	}
	
	/**
	 * Returns the name of an album
	 * @since 0.13.0
	 * 
	 * @param $id int Album id
	 * 
	 * @return String Name of album
	 */
	protected function getAlbumName($id, $plus = false){
		$sql = "SELECT name FROM albums WHERE id = " . $id;
		$result = mysql_query($sql);
		
		if(mysql_num_rows($result) < 1){
			return "No Album";
		}
		
		$row = mysql_fetch_assoc($result);
		
		if($plus == true)
			$row['name'] = str_replace(" ", "+", $row['name']);
		
		return $row['name'];
	}
	
	/**
	 * Return the id of an album based off of it's name
	 * @since 0.13.0
	 * 
	 * @param $name String Album name
	 * 
	 * @return int Album ID
	 */
	protected function getAlbumID($name){
		$sql = "SELECT id FROM albums WHERE name = '" . $name . "' LIMIT 1";
		$result = mysql_query($sql);
		
		if(mysql_num_rows($result) == 1){
			$row = mysql_fetch_assoc($result);
			
			return $row['id']; 
		}
		
		return 0;
	}
	
	/**
	 * Returns all of the albums currently in the database
	 * @since 0.9.0
	 * 
	 * @param $noAlbum bool Include "No Album" in list
	 * 
	 * @return array All of the albums in their own arrays, with 'id' and 'name'
	 */
	protected function getAlbums($noAlbum = true){
		$sql = "SELECT id, name FROM albums WHERE 1";
		$result = mysql_query($sql);
		
		$albums = array();
		
		if($noAlbum == true)
			$albums[] = array("id" => 0, "name" => "No album");
			
		if(mysql_num_rows($result) < 1)
			return $albums;
		
		while($row = mysql_fetch_assoc($result)){
			$albums[] = array("id"	 => $row['id'],
							  "name" => $row['name']
							  );	
		}
		
		return $albums;
	}
}

class AlbumsAdmin extends Albums{
	public function __construct(&$lncln){
		parent::__construct($lncln);
	}
	
	/**
	 * Add an album
	 * @since 0.13.0
	 */
	public function add(){
		if(isset($_POST['name'])){
			echo $this->addAlbum($_POST['name']);
		}
		
		$form = array(
			'action' => 'admin/Albums/add',
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Add Album',
			);
		
		$form['inputs'][] = array(
			'title' => 'Add new Album',
			'type' => 'text',
			'name' => 'name',
			);
		
		echo create_form($form);
	}
	
	/**
	 * Manage your albums
	 * @since 0.13.0
	 */
	public function manage(){
		if($this->lncln->params[2] == "delete"){
			$this->deleteAlbum($this->lncln->params[3]);
			$this->lncln->display->message("Album deleted<br />Please click <a href='" . URL . "admin/Albums/manage'>here</a> to continue.");
		}

		echo "Albums: <br />\n<ul>";
		foreach($this->getAlbums(false) as $album){
			echo "\t\t\t<li>" . $album['name'] . " <a href='" . URL . "admin/Albums/edit/" . $album['id'] . "'>Edit</a> " .
					"<a href='" . URL . "admin/Albums/manage/delete/" . $album['id'] . "'>Delete</a></li>\n";
		}	
		echo "</ul>";
			
	}
	
	public function edit(){
		if(!isset($this->lncln->params[2])){
			$this->lncln->display->message("Please don't come here on your own.");
		}
		
		if(isset($_POST['name'])){
			$this->lncln->display->message($this->changeAlbumName($_POST['id'], $_POST['name']) .
				"  Click <a href='" . URL . "admin/Albums/manage'>here</a> to continue");
		}
		
		$id = $this->lncln->params[2];
		$album = $this->getAlbumName($id);
		
		$form = array(
			'action' => 'admin/Albums/edit/' . $id,
			'method' => 'post',
			'inputs' => array(),
			'file' => false,
			'submit' => 'Edit Album',
			);
		
		$form['inputs'][] = array(
			'title' => '',
			'type' => 'hidden',
			'name' => 'id',
			'value' => $id,
			);
			
		$form['inputs'][] = array(
			'title' => 'Album Name:',
			'type' => 'text',
			'name' => 'name',
			'value' => $album,
			);
		
		echo create_form($form);
	}
	
	/**
	 * Registers actions that will be used in the admin panel
	 * @since 0.13.0
	 * 
	 * @return array Keys: url 
	 */
	public function actions(){
		$action = array(
			'urls' => array(
				'add' => 'Add album',
				'manage' => 'Manage albums',
				'edit' => '',
				),
			);
		
		return $action;
	}
	
	/**
	 * Adds an album to the database
	 * @since 0.9.0
	 * 
	 * @param string $name The name of the album
	 * 
	 * @return string If it passed or not
	 */
	protected function addAlbum($name){
		$name = prepareSQL($name);
		
		$sql = "SELECT COUNT(name) as name FROM albums WHERE name = '" . $name ."'";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['name'] > 0){
			return "Album already exists";
		}
		
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
	 * @since 0.9.0
	 * 
	 * @param int $album The album id to be deleted
	 */
	protected function deleteAlbum($album){
		$album = prepareSQL($album);
		
		if(is_numeric($album)){
			$sql = "UPDATE images SET album = 0 WHERE album = " . $album;
			mysql_query($sql);
			
			$sql = "DELETE FROM albums WHERE id = " . $album;
			mysql_query($sql);
		}		
	}
	
	/**
	 * Changes the album's name
	 * @since 0.11.0
	 * 
	 * @param $id int Album id
	 * @param $name string New name
	 */
	protected function changeAlbumName($id, $name){
		$id = prepareSQL($id);
		$name = prepareSQL($name);
		
		$sql = "SELECT COUNT(name) as name FROM albums WHERE name = '" . $name ."'";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['name'] > 0){
			return "Album already exists.";
		}
		
		$sql = "UPDATE albums SET name = '" . $name ."' WHERE id = " . $id;
		mysql_query($sql);
		
		return "Album updated successfully.";
	}
}