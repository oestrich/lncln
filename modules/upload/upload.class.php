<?php
/**
 * upload.class.php
 * 
 * Main file for the Upload module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
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
		if($this->lncln->user->permissions['refresh'] == 0)
			return "Cannot refresh image";
		
		if($data[1] != "refresh")
			return false;
		
		$query = array(
			'type' => 'SELECT',
			'fields' => array('type'),
			'table' => 'images',
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
		
		$this->lncln->thumbnail($id . "." . $row['type']);
	}

	/**
	 * Shows the upload box
	 * @since 0.13.0
	 * 
	 * @return string Upload box
	 */
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
	
	/**
	 * Delete icon
	 * @since 0.13.0
	 * 
	 * @param int $id Image ID
	 * 
	 * @return string Icon underneath the image
	 */
	public function icon($id){
		if($this->lncln->user->permissions['refresh'] == 0)
			return "";
		
		$output  = "<a href='" . URL . "action/upload/refresh/" . $id . "' onclick=\"return confirm('Are you sure you want to refresh?');\">\n";
		$output .= "<img src='" . URL . "theme/" . THEME . "/images/refresh.png' alt='Refresh' title='Refresh' style='border: none;'/>\n";
		$output .= "</a>\n";
		
		return $output;
	}

	/**
	 * Main page of the upload process
	 * @since 0.13.0
	 */
	public function index(){
		if($this->lncln->params[0] == "finish"){
			foreach($_POST['check'] as $key => $value){
				$this->finish_upload($key, $_POST['images'][$key]);
			}
			header("location:" . URL . "index/");
			exit();
		}
		
		if($this->lncln->params[0] == "cancel"){
			foreach($_POST['images'] as $image){
				@unlink(CURRENT_IMG_TEMP_DIRECTORY . $image);
			}
			
			unset($_SESSION['uploaded']);
			unset($_SESSION['upload']);
			unset($_SESSION['uploadTime']);
			unset($_SESSION['uploadKey']);
			
			header("location:" . URL . "index/");
			exit();
		}
		
		//Makes sure users don't come to this page without being sent here.  
		//Otherwise things might get messed up.
		if(!isset($_POST['type'])){
			$this->lncln->display->message(
				"Please don't come to this page on your own.  If you didn't come " .
				"here on your own, you may have uploaded more than " .
				ini_get("upload_max_filesize")
				);
		}
		
		$this->start_upload();
		
		echo "Tags are manditory.<br /><br /><br />\n";
		echo "<form action='" . URL . "upload/finish/' method='post'>\n";
		
		foreach($this->uploaded as $image){
			if(is_array($image)){
				if($image['error'] == "404")
					echo "<br />Image located at " . $image['image'] . " is 404\n<br /><br />";
				continue;
			}
	
			$size = getimagesize(CURRENT_IMG_TEMP_DIRECTORY . $image);
			$tHeight = ($size[1] / $size[0]) * 150;
			if($tHeight > 150){
				$thumb =  " height='150' ";
			}else{
				$thumb = " width='150' ";
			}
	
			echo "\t<div id='" . $image['id'] . "' class='modDiv'>\n";
			echo "\t\t<input type='hidden' name='check[" . $image . "]' value='" . $image . "' />\n";
			echo "\t\t<a href='" . URL . "images/temp/" . $image . "' target='_blank' class='modImage'>" .
					"<img src='" . URL . "images/temp/" . $image . "' " . $thumb . "/></a>\n";
			echo "\t\t<div class='modForms'>\n";
			echo "\t\t\t<input type='hidden' name='images[" . $image . "][id]' value='" . $image . "' /><br />\n";
			echo "\t\t\t<table>\n";
			foreach($this->lncln->modules as $module){
				if(!method_exists($module, "upload")){
					continue;
				}
				if($module->upload() == ""){
					continue;
				}
			
				echo "\t\t\t\t<tr>\n";
				echo "\t\t\t\t\t<td>" . $module->displayName . ":</td>\n";
				echo "\t\t\t\t\t<td>" . createInput($module->upload(), $image) . "</td>\n";
				echo "\t\t\t\t</tr>\n";
			}
			echo "\t\t\t</table>\n";
			echo "\t\t</div>\n";
			echo "\t</div>\n";
		}
		
		echo "\t<input type='submit' value='Submit' />\n";
		echo "</form>\n";
		
		echo "<form action='" . URL . "upload/cancel' method='post' />\n";
		echo "\t<div>\n";
		foreach($this->uploaded as $image){
			echo "\t\t<input type='hidden' name='images[]' value='" . $image . "' />\n";
		}
		echo "\t\t<input type='submit' value='Cancel' />\n";
		echo "\t</div>\n";
		echo "</form>\n";
	}
	
	/**
	 * The first part of uploading, moves the image to a temporary spot
	 * This allows for taging, captions, etc
	 * @since 0.10.0
	 */
	protected function start_upload(){
	 	$_SESSION['uploaded'] = true;
		$_SESSION['pages'] = 0;
	 	
	 	for($i = 0; $i < 10; $i++){
	 		//if nothing in either style uploads
			if($_POST['upload' . $i] == "" && $_FILES['upload'.$i]['name'] == ""){
				$_SESSION['upload'][$i] = 0;
				continue;
			}
			
			//Splitting the entire name, so it can pull the extension next
			$typeTmp = $_GET['url'] ? split("\.", $_POST['upload' . $i]) : split("\.", $_FILES['upload'.$i]['name']);
			
	        //the file extension
			$type = $typeTmp[count($typeTmp) - 1];
			
			//only these types
			if($type == "png" || $type == "jpg" || $type == "gif"){
				if($_GET['url']){
					$file = @file_get_contents($_POST['upload' . $i]);
					if(!$file){
						$_SESSION['upload'][$i] = 5;
						$this->uploaded[] = array("error" => "404", "image" => $_POST['upload' . $i]);
						continue;
					}
					$tempName = split("\/", $_POST['upload' . $i]);

					$tempName = $tempName[count($tempName) - 1];
					
					$name = tempName($tempName);
				}
				else{
					$name = tempName($_FILES['upload' . $i]['name']);
				}
				
				if($_GET['url']){
					file_put_contents(CURRENT_IMG_TEMP_DIRECTORY . $name, $file);
				}
				else{
					move_uploaded_file($_FILES['upload'.$i]['tmp_name'], CURRENT_IMG_TEMP_DIRECTORY . $name);
				}
				
				$this->uploaded[] = $name;
				
				$_SESSION['uploadKey'][$name] = $i;
			}
			else{
				$_SESSION['upload'][$i] == 4;
			}
	 	}
	}
	
	/**
	 * Uploads the pictures that the user fills in.  Whether it be from a URL or 
	 * direct input.
	 * @since 0.5.0
	 */
	protected function finish_upload($name, $data){
		if($data['tags'] == ""){
			$_SESSION['upload'][$_SESSION['uploadKey'][$name]] = 3;
			unlink(CURRENT_IMG_TEMP_DIRECTORY . $name);
			return "";
		}
		
		$sql = "SELECT MAX(postTime) FROM images";
		$this->db->query($sql);
		$row = $this->db->fetch_one();
					
		$postTime = time() >= ($row['MAX(postTime)'] + (60 * $this->display->settings['tbp'])) ? time() : $row['MAX(postTime)'] + (60 * $this->display->settings['tbp']);

		$this->lncln->user->checkUploadLimit(true);

		$typeTmp = split("\.", $name); 
		$type = $typeTmp[count($typeTmp) - 1];

		if($this->lncln->user->permissions['toQueue'] == 0){
			$sql = "INSERT INTO images (postTime, type, queue) VALUES (" . $postTime . ", '" . $type . "', 0)";
			$_SESSION['upload'][$_SESSION['uploadKey'][$name]] = 1;
		}
		else{
			$sql = "INSERT INTO images (postTime, type) VALUES (" . $postTime . ", '" . $type . "')";
			$_SESSION['upload'][$_SESSION['uploadKey'][$name]] = 2;
		}
		
		$_SESSION['uploadTime'][$_SESSION['uploadKey'][$name]] = $postTime;
		
		$this->db->query($sql);
		
		$imgID = str_pad($this->db->insert_id(), 6, 0, STR_PAD_LEFT);
				
		$_SESSION['image'][$_SESSION['uploadKey'][$name]] = $imgID . '.' . $type;
		
		rename(CURRENT_IMG_TEMP_DIRECTORY . $name, CURRENT_IMG_DIRECTORY . $imgID . '.' . $type);
		
		foreach($this->lncln->modules as $key => $module){
			if(method_exists($module, "add")){
				$module->add($imgID, array($data[$key]));
			}
		}
		
		$this->lncln->thumbnail($imgID . '.' . $type);
	}
}
