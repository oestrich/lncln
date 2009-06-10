<?
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
	 * @var Database Reference to the Database instance
	 */
	public $db = null;
	
	/**
	 * Construct to pass the reference of lncln so that modules 
	 * can access permissions and settings
	 * @since 0.13.0
	 * 
	 * @param lncln &$lncln Main class variable
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
		$this->prepareImage();
		
		$this->lncln->display->includeFile("header.php");
		
		$this->lncln->img();
		
		$image = (int)$this->lncln->params[0];
		
		if(isset($image) && is_numeric($image)){
			$this->lncln->display->includeFile("listing.php");	
		}
		else{
			echo "No such image.";
		}
		$this->lncln->display->includeFile("footer.php");
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
				
		$this->lncln->page = $image;
		$this->lncln->maxPage = 1;
		
		$_SESSION['thumbnail'] = 0;
	}
}
