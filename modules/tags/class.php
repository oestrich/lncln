<?
/**
 * module.php
 * 
 * Contains the interface for modules
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

class Tags implements Module{
	public $name = "Tags"; //Name printed out in forms
	public $displayName = "Tags";
	
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
	
	public function index(){
		if(isset($_POST['search']) && !isset($_GET['search'])){
			header("location:" . URL . "index.php?module=tags&search=" . $_POST['search']);
			exit();
		}
		
		if($_GET['search'] == ""){
			header("location:" . URL . "index.php");
			exit();
		}
		
		$this->search();
		
		$this->lncln->display->includeFile("iconActions.php");
		
		$this->lncln->img();
		
		$this->lncln->display->includeFile("header.php");
		
		echo "You searched for: " . $this->search . "<br />";
		
		echo $this->lncln->prevNext();
		
		$this->lncln->display->includeFile("listing.php");
		
		echo $this->lncln->prevNext(true);
		
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
		$id = mysql_real_escape_string($id);
		
		$tags = split(',', $data[0]);
		$tags = array_map('trim', $tags);
		$tags = array_map('prepareSQL', $tags);
		
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
	 * Called during the upload screen. Contains the form information needed,
	 * will be passed to add() after successful upload
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @return array Keys: type, name, value
	 */
	public function upload(){
		return array("type" => "text", "name" => "tags", "value" => "");
	}
	
	/**
	 * Creates the form information needed during moderation
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function moderate($id){
		return array("type" => "text", "name" => "tags", "value" => $this->getTags($id, true));
	}
	
	/**
	 * Creates the link in the header
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @return string Contains the form to do a search
	 */
	public function headerLink(){
		return "
					<form id='search' enctype='multipart/form-data' action='" . URL . "index.php?module=tags' method='post'>
						<div>
							Tag search:
							<input type='text' name='search' />
							<input type='submit' value='Search' />
						</div>
					</form> ";
	}
	
	/**
	 * Creates the icon underneath images
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function icon($id, $action){
		return "";
	}
	
	/**
	 * Creates text above the image.  Text only
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function aboveImage($id, $action){
		return "";
	}
	
	/**
	 * Creates text underneath the image.  May contain a form
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function underImage($id, $action){
		if($this->lncln->user->permissions['tags'] == 1){
			$classTag = " class='underImage'";
			$onClick = " onclick=\"showModule('" . $this->name . "', '" . $id . "');\"";
		}
		else{
			$classTag = "";
			$onClick = "";
		}
		
		$output = "
			<div id='tags$id'" . $classTag . $onClick . ">
				Tags: " . $this->getTags($id, true) . "
			</div>\n";
		
		if($this->lncln->user->permissions['tags'] == 1):
			$tags = $this->getTags($id, true);
			
			if($tags == "None.")
				$tags = "";
		
			$output .= "\t\t\t<form id='t$id' style='display: none;' action='$action&amp;action=tags' method='post'>
				<div>
					<input type='hidden' name='id' value='$id' />
					Split tags with a ','.<br />
					<input name='tags' id='formTags$id' value='$tags' size='85'/>
					<input type='submit' value='Tag it!' />
				</div>
			</form>\n";
		endif;
				
		return $output;
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */

	/**
	 * Sets up the pages for searching
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	function search(){
		$this->search = prepareSQL($_GET['search']);
		
		$this->lncln->scriptExtra = "search=" . $_GET['search'];
		
		$sql = "SELECT COUNT(*) FROM tags WHERE tag LIKE '%" . $this->search . "%'";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['COUNT(*)'] == 0){
			$this->page = 0;
		}
		else{		
			$sql = "SELECT COUNT(picId) FROM tags WHERE tag LIKE '%" . $this->search . "%'";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			$this->lncln->maxPage = $row['COUNT(picId)'];
			$this->lncln->maxPage = ceil($this->lncln->maxPage / $this->lncln->display->settings['perpage']);
			
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
			
			$sql = "SELECT picId FROM tags WHERE tag LIKE '%" . $this->search . "%' ORDER BY picId DESC LIMIT " . $offset . ", " . $this->lncln->display->settings['perpage'];
			$result = mysql_query($sql);
	
			while($row = mysql_fetch_assoc($result)){
				$this->lncln->imagesToGet[] = $row['picId'];
			}
		}
	}

	/**
	 * Gathers tags from an image together, string or array form
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int Image id
	 * @param $string bool String if true, array if false
	 * 
	 * @return mixed Array of tags or string joined by ','
	 */
	private function getTags($id, $string = false){
		$sql = "SELECT tag FROM tags WHERE picId = " . $id;
		$result = mysql_query($sql);
		
		$tags = array();
		
		if(mysql_num_rows($result) < 1){
			return $string ? "None." : array("None");
		}
		
		while($row = mysql_fetch_assoc($result)){
			$tags[] = $row['tag'];
		}
		
		if($string == false)
			return $tags;
		
		$tags = join(', ', $tags);
		
		return $tags;
	}
}
?>
