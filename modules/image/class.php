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

class Image implements Module{
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
		
		if(isset($_GET['image']) && is_numeric($_GET['image'])){
			$this->lncln->display->includeFile("listing.php");	
		}
		else{
			echo "No such image.";
		}
		$this->lncln->display->includeFile("footer.php");
	}
	

	/**
	 * Not used, no need to comment them further
	 */
	public function add($id, $data){
		
	}
	
	public function edit($id, $data){
		
	}
	
	public function upload(){
		return "";
	}
	
	public function moderate($id){
		return "";
	}
	
	public function headerLink(){
		return "";
	}
	
	public function icon($id, $action){
		return "";
	}
	
	public function aboveImage($id, $action){
		return "";
	}
	
	public function underImage($id, $action){
		return "";
	}

	public function rss($id){
		return "";
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
