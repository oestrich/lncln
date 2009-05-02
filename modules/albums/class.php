<?
/**
 * class.php
 * 
 * Contains the interface for modules
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

class Albums implements Module{
	public $name = "Albums";
	
	/**
	 * Construct to pass the reference of lncln so that modules 
	 * can access permissions and settings
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $lncln lncln Main class variable
	 */
	public function __construct(&$lncln){
		$this->lncln = $lncln;
	}
	
	/**
	 * Called if the Module has it's own page
	 * Such as albums or search
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function index(){
		$this->album();
		
		$this->lncln->display->includeFile("iconActions.php");
		
		$this->lncln->img();
		
		$this->lncln->display->includeFile("header.php");
		
		if(!isset($_GET['album']) || $_GET['album'] == ""){
			foreach($this->getAlbums(false) as $album){
		?>
					<a href="<?=$lncln->lncln->script;?>?module=albums&amp;album=<?=$album['id'];?>"><?=$album['name'];?></a><br />
		<?
			}
		}
		else{
			echo "You're viewing " . $this->getAlbumName($this->album) . "\n <br />";
			
			echo $this->lncln->prevNext();
			
			$this->lncln->display->includeFile("listing.php");

			echo $this->lncln->prevNext();
		}
		
		$this->lncln->display->includeFile("footer.php");
	}
	
	/**
	 * Called after a successful upload
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int ID of new image
	 * @param $data array Extra material needed, tag information, etc
	 */
	public function add($id, $data){
		$this->edit($id, $data);
	}
	
	/**
	 * Edits an image with the data provided
	 * 
	 * @since 0.13.0
	 * @package lncln
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
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function upload(){
		return array("type" => "select", "name" => "albums", "value" => "", "options" => $this->getAlbums());
	}
	
	/**
	 * Creates the form information needed during moderation
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int Image to gather information about and populate the input
	 */
	public function moderate($id){
		return array("type" => "select", "name" => "albums", "value" => $this->getAlbumName($id), "options" => $this->getAlbums());
	}
	
	/**
	 * Creates the link in the header
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function headerLink(){
		return "\t\t\t\t\t<a href='" . URL . "index.php?module=albums'>Albums</a>\n";
	}
	
	/**
	 * Creates the icon underneath images
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function icon($id){
		return "";
	}
	
	/**
	 * Creates text underneath the image.  May contain a form
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function underImage($id, $action){
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
				Album: " . $this->getAlbumName($id) . "
			</div>";
		
		
		if($this->lncln->user->permissions['albums'] == 1):
			$output .= "
			<form id='a$id' style='display: none;' action='$action&amp;action=albums' method='post'>
				<div>
					<input type='hidden' name='id' value='$id' />
					<select name='albums' id='formAlbums$id'>
						<option value='0'>No album</option>";
			foreach($this->getAlbums() as $album):
				$selected = $album['name'] == $this->getAlbumName($id) ? "selected" : "";
				$output .= "<option value='" . $album['id'] ."' $selected>" . $album['name'] . "</option>";
			endforeach;
			$output .= "
					</select>
					<input type='submit' value='Change album' />
				</div>
			</form>";
		endif;
		return $output;
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Function for loading albums
	 * 
	 * @since 0.9.0
	 * @package lncln
	 */
	function album(){	
		$album = $_GET['album'];
		
		if($album != 0){
			$this->album = prepareSQL($album);
			$time = !$this->lncln->user->permissions['isAdmin'] ? " AND postTime <= " . time() . " " : "";
			
			$sql = "SELECT COUNT(*) FROM images WHERE queue = 0 AND album = " . $this->album . $time;
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			if($row['COUNT(*)'] == 0){
				$this->lncln->page = 0;
			}
			else{				
				$sql = "SELECT COUNT(id) FROM images WHERE album = " . $this->album . $time;
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				
				$this->lncln->maxPage = ceil($row['COUNT(id)'] / $this->lncln->display->settings['perpage']);

				if(!isset($_GET['page'])){
					$this->lncln->page = 1;
				}
				else{
					if(is_numeric($_GET['page'])){
						$this->lncln->page = $_GET['page'];	
					}
					else{
						$this->lncln->page = 1;
					}
				}
				
				$offset = ($this->lncln->page - 1) * $this->lncln->display->settings['perpage'];
				
				$sql = "SELECT id FROM images WHERE album = " . $this->album . " AND queue = 0 " . $time. " ORDER BY id DESC LIMIT " . $offset . ", " . $this->lncln->display->settings['perpage'];
				$result = mysql_query($sql);
		
				while($row = mysql_fetch_assoc($result)){
					$this->lncln->imagesToGet[] = $row['id'];
				}
				
				$this->lncln->extra .= "&amp;album=" . $this->album;
			}
		}
	}
	
	/**
	 * Returns the name of an album based on an image
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int Image id
	 * 
	 * @return String Name of album
	 */
	private function getAlbumName($id){
		$sql = "SELECT album FROM images WHERE id = " . $id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['album'] == 0){
			return "No Album";
		}
		
		$sql = "SELECT name FROM albums WHERE id = " . $row['album'];
		$result = mysql_query($sql);
		
		if(mysql_num_rows($result) < 1){
			return "No Album";
		}
		
		$row = mysql_fetch_assoc($result);
		
		return $row['name'];
	}
	
	/**
	 * Returns all of the albums currently in the database
	 * 
	 * @since 0.9.0
	 * @package lncln
	 * 
	 * @param $noAlbum bool Include "No Album" in list
	 * 
	 * @return array All of the albums in their own arrays, with 'id' and 'name'
	 */
	function getAlbums($noAlbum = true){
		$sql = "SELECT id, name FROM albums WHERE 1";
		$result = mysql_query($sql);
		
		if($noAlbum == true)
			$albums[] = array("id" => 0, "name" => "No album");
		
		while($row = mysql_fetch_assoc($result)){
			$albums[] = array("id"	 => $row['id'],
							  "name" => $row['name']
							  );	
		}
		
		return $albums;
	}
}
?>
