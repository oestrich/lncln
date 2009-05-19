<?
/**
 * module.php
 * 
 * Tags module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

class Tags{
	public $name = "Tags"; //Name printed out in forms
	public $displayName = "Tags";
	
	public $searchTerm;
	
	public $db = null;
	
	public $values = array();
	
	/**
	 * Construct to pass the reference of lncln so that modules 
	 * can access permissions and settings
	 * @since 0.13.0
	 * 
	 * @param $lncln lncln Main class variable
	 */
	public function __construct(&$lncln){
		$this->db = get_db();
		
		$this->lncln = $lncln;
	}
	
	/**
	 * Called if the Module has it's own page
	 * Such as albums or search
	 * @since 0.13.0
	 */
	public function index(){
		if(isset($_POST['search']) && !isset($this->lncln->params[0])){
			header("location:" . URL . "tags/" . $this->addPluses($_POST['search']));
			exit();
		}
		
		if($this->lncln->params[0] == ""){
			header("location:" . URL . "index/");
			exit();
		}
		
		$this->search();
		
		$this->lncln->display->includeFile("iconActions.php");
		
		$this->lncln->img();
		
		$this->lncln->display->includeFile("header.php");
		
		echo "You searched for: " . $this->searchTerm . "<br />";
		
		echo $this->lncln->prevNext();
		
		$this->lncln->display->includeFile("listing.php");
		
		echo $this->lncln->prevNext(true);
		
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
		$id = mysql_real_escape_string($id);
		
		$tags = split(',', $data[0]);
		$tags = array_map('trim', $tags);
		$tags = array_map('prepareSQL', $tags);
		
		$sql = "DELETE FROM tags WHERE picId = " . $id;
		$this->db->query($sql);
		
		$sql = "INSERT INTO tags (picId, tag) VALUES ";
		
		foreach($tags as $tag){
			if($tag == ""){
				continue;
			}
			$sql .= "(" . $id . ", '" . $tag . "'), ";
		}
	
		$sql = substr_replace($sql ,"",-2);
		
		$this->db->query($sql);
	}
	
	/**
	 * Called during the upload screen. Contains the form information needed,
	 * will be passed to add() after successful upload
	 * @since 0.13.0
	 * 
	 * @return array Keys: type, name, value
	 */
	public function upload(){
		return array("type" => "text", "name" => "tags", "value" => "");
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
		return array("type" => "text", "name" => "tags", "value" => $this->get_tags($id, true));
	}
	
	/**
	 * Creates the link in the header
	 * @since 0.13.0
	 * 
	 * @return string Contains the form to do a search
	 */
	public function header_link(){
		return "
					<form id='search' enctype='multipart/form-data' action='" . URL . "tags/' method='post'>
						<div>
							Tag search:
							<input type='text' name='search' />
							<input type='submit' value='Search' />
						</div>
					</form> ";
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
				Tags: " . $this->get_tags($id, true) . "
			</div>\n";
		
		if($this->lncln->user->permissions['tags'] == 1):
			$tags = $this->get_tags($id, true);
			
			if($tags == "None.")
				$tags = "";
		
			$output .= "\t\t\t<form id='t$id' style='display: none;' action='" . URL . "action/tags/$id' method='post'>
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
	 * Pushes content out via the RSS feed
	 * @since 0.13.0
	 * 
	 * @param $id int Image ID
	 * 
	 * @return string Output for the RSS feed
	 */
	public function rss($id){
		return "Tags: " . $this->get_tags($id, true);
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */

	/**
	 * Sets up the pages for searching
	 * @since 0.13.0
	 */
	private function search(){
		$this->searchTerm = prepareSQL($this->removePluses($this->lncln->params[0]));
		
		$sql = "SELECT COUNT(*) FROM tags WHERE tag LIKE '%" . $this->searchTerm . "%'";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
		
		if($row['COUNT(*)'] == 0){
			$this->page = 0;
		}
		else{		
			$sql = "SELECT COUNT(picId) FROM tags WHERE tag LIKE '%" . $this->searchTerm . "%'";
			$this->db->query($sql);
			$row = $this->db->fetch_one();
			
			$this->lncln->maxPage = $row['COUNT(picId)'];
			$this->lncln->maxPage = ceil($this->lncln->maxPage / $this->lncln->display->settings['perpage']);
			
			$page = (int)end($this->lncln->params);
			
			if(!isset($page)){
				$this->lncln->page = 1;
			}
			else{
				if(is_numeric($page) && $page != ""){
					$this->lncln->page = $page;	
				}
				else{
					$this->lncln->page = 1;
				}
			}
			
			$offset = ($this->lncln->page - 1) * $this->lncln->display->settings['perpage'];
			
			$sql = "SELECT picId FROM tags WHERE tag LIKE '%" . $this->searchTerm . "%' ORDER BY picId DESC LIMIT " . $offset . ", " . $this->lncln->display->settings['perpage'];
			$this->db->query($sql);
	
			foreach($this->db->fetch_all() as $row){
				$this->lncln->imagesToGet[] = $row['picId'];
			}
		}
	}

	/**
	 * Gathers tags from an image together, string or array form
	 * @since 0.13.0
	 * 
	 * @param $id int Image id
	 * @param $string bool String if true, array if false
	 * 
	 * @return mixed Array of tags or string joined by ','
	 */
	private function get_tags($id, $string = false){
		if(array_key_exists($id, $this->values)){
			$tags = $this->values[$id];
		}
		else{		
			$sql = "SELECT tag FROM tags WHERE picId = " . $id;
			$this->db->query($sql);
			
			$tags = array();
			
			if($this->db->num_rows() < 1){
				return $string ? "None." : array("None");
			}
			
			foreach($this->db->fetch_all() as $row){
				$tags[] = $row['tag'];
			}
			
			$this->value[$id] = $tags;
		}
		
		if($string == false)
			return $tags;
		
		$tags = join(', ', $tags);
		
		return $tags;
	}
	
	/**
	 * Quick shortcut to replace spaces with plus signs
	 * @since 0.13.0
	 * 
	 * @param $string String search string
	 * 
	 * @return string string with pluses
	 */
	private function addPluses($string){
		return str_replace(" ", "+", $string);
	}
	
	/**
	 * Opposite of above
	 */
	private function removePluses($string){
		return str_replace("+", " ", $string);
	}
}
?>
