<?
/**
 * class.php
 * 
 * Image module, for displaying only one image
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

class Image{
	public $name = "Image"; //Name printed out in forms
	public $displayName = "Image";
	
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
		$this->prepareImage();

		$this->lncln->display->includeFile("iconActions.php");
		
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
			$image = prepareSQL($image);
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
?>
