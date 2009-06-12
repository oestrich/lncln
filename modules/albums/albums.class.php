<?
/**
 * class.php
 * 
 * Albums module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Main class for the Albums module
 * @since 0.13.0
 * 
 * @package lncln
 */
class Albums extends Module{
	/**
	 * @var string Module name
	 */
	public $name = "Albums";
	
	/**
	 * @var string Display name for module
	 */
	public $displayName = "Album";

	/**
	 * @var array Storing values from database
	 */
	public $values = array(
		'image_album' => array(),
		'album_name' => array(),
		);
	
	/**
	 * Called if the Module has it's own page
	 * Such as albums or search
	 * @since 0.13.0
	 */
	public function index(){
		$this->album();
		
		$this->lncln->get_data();
		
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
			
			$this->lncln->display->show_posts();
		}
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
		$img = $this->db->prep_sql($id);
		$album = $this->db->prep_sql($data[0]);
		
		$query = array(
			'type' => 'UPDATE',
			'table' => 'images',
			'set' => array(
				'album' => $album,
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
	 * @param int $id Image to gather information about and populate the input
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
	 * Creates text underneath the image.  May contain a form
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
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
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Function for loading albums
	 * @since 0.9.0
	 */
	protected function album(){	
		$album = $this->db->prep_sql($this->lncln->params[0]);
		
		$album = $this->getAlbumId($album);
		
		if($album != 0){
			$time = !$this->lncln->user->permissions['isAdmin'] ? array('field' => 'postTime', 'compare' => '<=', 'value' => time()): array();
			
			$query = array(
				'type' => 'SELECT',
				'fields' => array('!COUNT(id)'),
				'table' => 'images',
				'where' => array(
					'AND' => array(
						array(
							'field' => 'queue',
							'compare' => '=',
							'value' => 0,
							),
						array(
							'field' => 'album',
							'compare' => '=',
							'value' => $album,
							),
						$time,
						),
					),
				);
			
			$this->db->query($query);
			$row = $this->db->fetch_one();
			
			if($row['COUNT(id)'] == 0){
				$this->lncln->page = 0;
			}
			else{
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
				
				$query = array(
					'type' => 'SELECT',
					'fields' => array('id'),
					'table' => 'images',
					'where' => array(
						'AND' => array(
							array(
								'field' => 'queue',
								'compare' => '=',
								'value' => 0,
								),
							array(
								'field' => 'album',
								'compare' => '=',
								'value' => $album,
								),
							$time,
							),
						),
					'order' => array(
						'DESC', 
						array('id'),
						),
					'limit' => array($offset, $this->lncln->display->settings['perpage']),
					);
				
				$this->db->query($query);
		
				foreach($this->db->fetch_all() as $row){
					$this->lncln->imagesToGet[] = $row['id'];
				}
			}
		}
	}
	
	/**
	 * Get an album name based on an image
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return string Album name
	 */
	protected function getImageAlbum($id){
		foreach($this->lncln->images as $image){
			if($image['id'] == $id){
				$album = $image['album'];
			}
		}
		
		if($album == 0){
			return "No Album";
		}
				
		return $this->getAlbumName($album);
	}
	
	/**
	 * Returns the name of an album
	 * @since 0.13.0
	 * 
	 * @param int $id Album id
	 * 
	 * @return string Name of album
	 */
	protected function getAlbumName($id, $plus = false){
		
		if(array_key_exists($id, $this->values['album_name'])){
			$row = $this->values['album_name'][$id];
		}
		else{
			$query = array(
				'type' => 'SELECT',
				'fields' => array('name'),
				'table' => 'albums',
				'where' => array(
					array(
						'field' => 'id',
						'compare' => '=',
						'value' => $id,
						),
					),
				);
			
			$this->db->query($query);
			
			$row = $this->db->fetch_one();
			$this->values['album_name'][$id] = $row;
		}
		
		if($plus == true)
			$row['name'] = str_replace(" ", "+", $row['name']);
		
		return $row['name'];
	}
	
	/**
	 * Return the id of an album based off of it's name
	 * @since 0.13.0
	 * 
	 * @param string $name Album name
	 * 
	 * @return int Album ID
	 */
	protected function getAlbumID($name){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('id'),
			'table' => 'albums',
			'where' => array(
				array(
					'field' => 'name',
					'compare' => '=',
					'value' => $name,
					),
				),
			'limit' => array(1),
			);
		
		$this->db->query($query);
		
		if($this->db->num_rows() == 1){
			$row = $this->db->fetch_one();
			
			return $row['id']; 
		}
		
		return 0;
	}
	
	/**
	 * Returns all of the albums currently in the database
	 * @since 0.9.0
	 * 
	 * @param bool $noAlbum Include "No Album" in list
	 * 
	 * @return array All of the albums in their own arrays, with 'id' and 'name'
	 */
	protected function getAlbums($noAlbum = true){
		
		if(array_key_exists("albums", $this->values)){
			$albums = $this->values['albums'];
		}
		else{
			$query = array(
				'type' => 'SELECT',
				'fields' => array('id', 'name'),
				'table' => 'albums',
				);
			
			$this->db->query($query);
			
			$albums = array();
			
			if($noAlbum == true)
				$albums[] = array("id" => 0, "name" => "No album");
				
			if($this->db->num_rows() < 1)
				return $albums;
			
			foreach($this->db->fetch_all() as $row){
				$albums[] = array("id"	 => $row['id'],
								  "name" => $row['name']
								  );	
			}
			$this->values['albums'] = $albums;
		}
		return $albums;
	}
}
