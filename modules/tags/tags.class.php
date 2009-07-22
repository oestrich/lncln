<?php
/**
 * module.php
 * 
 * Tags module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Main class for the Tags module
 * @since 0.13.0
 * 
 * @package lncln
 */
class Tags extends Module{
	/**
	 * @var string Name of module
	 */
	public $name = "Tags";
	
	/**
	 * @var string Display name for module
	 */
	public $displayName = "Tags";
	
	/**
	 * @var string Search term
	 */
	public $searchTerm;
	
	/**
	 * Called if the Module has it's own page
	 * Such as albums or search
	 * @since 0.13.0
	 */
	public function index(){
		if(isset($_POST['search']) && $this->lncln->params[0] == ""){
			header("location:" . URL . "tags/" . $this->addPluses($_POST['search']));
			exit();
		}
		
		if($this->lncln->params[0] == ""){
			//header("location:" . URL . "index/");
			exit();
		}
		
		$this->search();
		
		$this->lncln->get_data();
		
		echo "You searched for: " . $this->searchTerm . "<br />";
		
		$this->lncln->display->show_posts();
	}
	
	/**
	 * Called after a successful upload
	 * @since 0.13.0
	 * 
	 * @param int $id ID of new image
	 * @param array $data Extra material needed, tag information, etc
	 */
	public function add($id, $data){
		$this->edit($id, $data);
	}
	
	/**
	 * Edits an image with the data provided
	 * @since 0.13.0
	 * 
	 * @param int $id ID of image
	 * @param array $data Extra material needed, tag information, etc
	 */	
	public function edit($id, $data){
		$id = mysql_real_escape_string($id);
		
		$tags = split(',', $data[0]);
		$tags = array_map('trim', $tags);
		$prep_sql = $this->db->prep_sql;
		$tags = array_map($prep_sql, $tags);
		
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
		
		$this->set_tags($id);
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
		$output = "<form id='search' enctype='multipart/form-data' action='" . URL . "tags/' method='post'>\n";
		$output .= "\t<div>\n";
		$output .= "\t\tTag search:\n";
		$output .= "\t\t<input type='text' name='search' />\n";
		$output .= "\t\t<input type='submit' value='Search' />\n";
		$output .= "\t</div>\n";
		$output .= "</form>\n";
		
		return $output;
	}
	
	/**
	 * Creates text underneath the image.  May contain a form
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
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
	 * @param int $id Image ID
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
		$this->searchTerm = $this->db->prep_sql($this->removePluses($this->lncln->params[0]));

		$query = array(
			'type' => 'SELECT',
			'fields' => array('!COUNT(*)'),
			'table' => 'tags',
			'where' => array(
				array(
					'field' => 'tag',
					'compare' => 'LIKE',
					'value' => "%" . $this->searchTerm . "%",
					),
				),
			);
		
		$this->db->query($query);
		$row = $this->db->fetch_one();
		
		if($row['COUNT(*)'] == 0){
			$this->page = 0;
		}
		else{
			$query = array(
				'type' => 'SELECT',
				'fields' => array('!COUNT(picId)'),
				'table' => 'tags',
				'where' => array(
					array(
						'field' => 'tag',
						'compare' => 'LIKE',
						'value' => '%' . $this->searchTerm . '%',
						),
					),
				);
			
			$this->db->query($query);
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
			
			$query = array(
				'type' => 'SELECT',
				'fields' => array('picId'),
				'table' => 'tags',
				'where' => array(
					array(
						'field' => 'tag',
						'compare' => 'LIKE',
						'value' => '%' . $this->searchTerm . '%',
						),
					),
				'order' => array('DESC', array('picId')),
				'limit' => array(
					$offset,
					$this->lncln->display->settings['perpage'],
					),
				);
			
			$this->db->query($query);
	
			foreach($this->db->fetch_all() as $row){
				$this->lncln->imagesToGet[] = $row['picId'];
			}
		}
	}

	/**
	 * Gathers tags from an image together, string or array form
	 * @since 0.13.0
	 * 
	 * @param int $id Image id
	 * @param bool $string String if true, array if false
	 * 
	 * @return mixed Array of tags or string joined by ','
	 */
	private function get_tags($id, $string = false){
		foreach($this->lncln->images as $image){
			if($image['id'] == $id){
				if($image['tags'] == ""){
					$image['tags'] = $this->set_tags($id);
				}

				return $image['tags'];
			}
		}
		
		return "";
	}
	
	/**
	 * Sets the tags field in the image table with current tags from tags table
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return string Current tags
	 */
	private function set_tags($id){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('tag'),
			'table' => 'tags',
			'where' => array(
				array(
					'field' => 'picId',
					'compare' => '=',
					'value' => $id,
					),
				),
			);
		
		$this->db->query($query);
		
		$tags = array();
		
		foreach($this->db->fetch_all() as $row){
			$tags[] = $row['tag'];
		}
		
		$tags = join(', ', $tags);
		
		$query = array(
			'type' => 'UPDATE',
			'table' => 'images',
			'set' => array(
				'tags' => $tags,
				),
			'where' => array(
				array(
					'field' => 'id',
					'compare' => '=',
					'value' => $id,
					),
				),
			);
		$this->db->query($query);

		return $tags;
	}
	
	/**
	 * Quick shortcut to replace spaces with plus signs
	 * @since 0.13.0
	 * 
	 * @param string $string search string
	 * 
	 * @return string String with pluses
	 */
	private function addPluses($string){
		return str_replace(" ", "+", $string);
	}
	
	/**
	 * Quick shortcut to replace pluses with spaces
	 * @since 0.13.0
	 * 
	 * @param string $string search string
	 * 
	 * @return string String with spaces
	 */
	private function removePluses($string){
		return str_replace("+", " ", $string);
	}
}
