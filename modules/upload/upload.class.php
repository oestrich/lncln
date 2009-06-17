<?php
/**
 * upload.class.php
 * 
 * Main file for the Upload module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Main class for Upload module
 * @since 0.13.0
 * 
 * @package lncln
 */
class Upload extends Module{
	/**
	 * @var string Name of module, Used in forms
	 */
	public $name = "Upload";
	
	/**
	 * @var string Display name of module
	 */
	public $displayName = "Upload";

	public function header_link(){
		if($this->lncln->user->permissions['upload'] == 1){
			$output = "Upload: <a href='javascript:;' onmousedown='toggleDiv(\"regular\")'>File</a> <a href='javascript:;' onmousedown='toggleDiv(\"url\")'>URL</a>\n";
			$output .= "We have " . $this->lncln->get_num_images() . " images.";
		
			$output .= "<!-- upload form -->\n";
			$output .= "<form enctype='multipart/form-data' action='" . URL . "upload/' method='post'  id='form' style='display: none;'>\n";
			$output .= "\t<div>\n";
			$output .= "\t\t<input type='hidden' name='type' id='formType' value='regular' />\n";
			$output .= "\t\tUploaded files will be moderated<br />\n";
			for($a = 0; $a < 10; $a++){
				$output .= "\t\t<input name='upload$a' id='upload$a' type='file' />";
				$output .= "<br />\n";
			}
			$output .= "\t\t<input type='submit' value='Upload File' />\n";
			$output .= "\t\t<br />\n";
			$output .= "\t\tMax total upload size:" . ini_get("upload_max_filesize") . "\n";
			$output .= "\t</div>\n";
			$output .= "</form>\n";
		}
		
		return $output;
	}


}
