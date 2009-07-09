<?php
/**
 * class.php
 * 
 * Image module, for displaying only one image
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Main class for the image module
 * @since 0.13.0
 * 
 * @package lncln
 */
class Image extends Module{
	/**
	 * @var string Name of module
	 */
	public $name = "Image";
	
	/**
	 * @var string Display name for module
	 */
	public $displayName = "Image";
	
	/**
	 * Called if the Module has it's own page
	 * Such as albums or search
	 * @since 0.13.0
	 */
	public function index(){		
		$this->prepareImage();
		
		$this->lncln->get_data();
		
		$image = (int)$this->lncln->params[0];
		
		if(isset($image) && is_numeric($image)){
			$this->lncln->display->show_posts();	
		}
		else{
			echo "No such image.";
		}
	}
	
	/**
	 * Action for deleting images
	 * @since 0.13.0
	 * 
	 * @param int $id ID of image
	 * @param array $data Extra material needed, tag information, etc
	 * 
	 * @return string Completion message
	 */
	public function edit($id, $data){
		if($data[1] == "delete"){
			if($this->lncln->user->permissions['delete'] == 0)
				return "Cannot delete";
			
			return $this->delete($id);
		}
	}
	
	/**
	 * Delete icon
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return string Icon underneath the image
	 */
	public function icon($id){
		if($this->lncln->user->permissions['delete'] == 1){
			$output = "<a href='" . URL . "action/image/delete/" . $id . "' " .
						"onclick=\"return confirm('Are you sure you want to delete this?');\">\n";
			$output .= "\t<img src='" . URL . "theme/" . THEME . "/images/delete.png' " .
						"alt='Delete' title='Delete' style='border: none;'/>\n";
			$output .= "</a>\n";
		}
		
		return $output;
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Function that makes image.php go round
	 * @since 0.9.0
	 */
	private function prepare_image(){
		$image = (int)$this->lncln->params[0];
		
		if(isset($image) && is_numeric($image)){
			$image = $this->db->prep_sql($image);
		}
		else{
			return;
		}
		
		$this->lncln->increaseView($image);

		$this->lncln->imagesToGet[] = $image;
				
		$this->lncln->page = 1;
		$this->lncln->maxPage = 1;
		
		$_SESSION['thumbnail'] = 0;
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
	protected function delete($image){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('type'),
			'table' => 'images',
			'where' => array(
				array(
					'field' => 'id',
					'compare' => '=',
					'value' => $image,
					),
				),
			'limit' => array(1),
			);
		
		$this->db->query($query);
		
		if($this->db->num_rows() == 1){
			$type = $this->db->fetch_one();
		}
		else{
			return "No such image.";
		}
	
		$sql = "DELETE FROM images WHERE id = " . $image . " LIMIT 1";
		$this->db->query($sql);
		
		//use an @ sign so that it won't throw an error, probably meaning it wasn't there to begin with
		@unlink(ABSPATH . "images/full/" . $image . "." . $type['type']);
		@unlink(ABSPATH . "images/thumb/" . $image . "." . $type['type']);
		@unlink(ABSPATH . "images/index/" . $image . "." . $type['type']);
		
		return "Successfully deleted.";
	}
}
