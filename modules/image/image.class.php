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
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Function that makes image.php go round
	 * @since 0.9.0
	 */
	private function prepareImage(){
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
}
