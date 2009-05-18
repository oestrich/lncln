<?
/**
 * class.php
 * 
 * Index module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */

class Index{
	public $name = "Index"; //Name printed out in forms
	public $displayName = "Index";
	
	public $db = null;
	
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
		$this->prepare_index();

		$this->lncln->img();
		
		$this->lncln->display->set_title("Index");
		
		$this->lncln->display->includeFile("header.php");
		
		$news = $this->lncln->getNews();
		?>	
			<div id="news"> 
				<span style="font-weight: bold;" onclick="news()">
					<?echo $news['title'];?> <span style="color: #22FF00">(click to show)</span>: on <?=date("m/d/Y", $news['postTime']);?>:
				</span>
				<br/>
				<p id="actualNews">
					<?echo $news['news'];?> 
				</p>
				<br />
			</div> 
		<?
		
		echo $this->upload_status();
		
		echo $this->lncln->prevNext();

		$this->lncln->display->includeFile("listing.php");
		
		echo $this->lncln->prevNext(true);
		
		$this->lncln->display->includeFile("footer.php");
	}
	
	/**
	 * Creates the link in the header
	 * @since 0.13.0
	 *
	 * @return string Link or form
	 */
	public function header_link(){
		$thumbnail = $_SESSION['thumbnail'] == 1 ? "off" : "on";
		
		return "\t\t\t\t<a href='" . URL . "index/'>Newest</a>
					<a href='" . URL . "thumbnail/" . $thumbnail . "'>Thumbnail view</a>";
	}
	
	/**
	 * Required functions above, Below are other useful ones 
	 * related to only this class
	 */
	
	/**
	 * Prints out upload status
	 * @since 0.13.0
	 */
	protected function upload_status(){
		if($_SESSION['uploaded']){
			for($i = 0; $i < 10; $i++){
				$a = $i + 1;
				switch($_SESSION['upload'][$i]){
					case 0:
						break;
					case 1:
						$date = date('h:i:s A - m/d/Y', $_SESSION['uploadTime'][$i] + (3 * 60 * 60));
						$output = "Uploaded #$a correctly. It will appear at $date. To see it now <a href='images/full/" . $_SESSION['image'][$i] . "'>click here</a>.<br />";
						break;
					case 2:
						$output = "Uploaded #$a to the queue. <br />";
						break;
					case 3:
						$output = "#$a is missing tags. <br />";
						break;
					case 4:
						$output = "#$a is the wrong file type. <br />";
						break;
					case 5:
						$output = "#$a got a 404 error. <br />";
						break;
				}
			}
			$_SESSION['pages'] += 1;
			
			//So it only shows up once
			if($_SESSION['pages'] >= 1){
				unset($_SESSION['uploaded']);
				unset($_SESSION['upload']);
				unset($_SESSION['uploadTime']);
				unset($_SESSION['uploadKey']);
			}
		}
		
		return $output;
	}
	
	
	/**
	 * The function that makes the index go round
	 * @since 0.9.0
	 */
	protected function prepare_index(){
		$this->lncln->moderationOn = true;
		$time = !$this->lncln->user->permissions['isAdmin'] ? " AND postTime <= " . time() . " " : "";
		
		$sql = "SELECT COUNT(*) FROM images WHERE queue = 0 " . $time;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if($row['COUNT(*)'] == 0){
			$this->page = 0;
		}
		else{
			$result = mysql_query("SELECT COUNT(id) FROM images WHERE queue = 0 " . $time);
			$row = mysql_fetch_assoc($result);

			$this->lncln->maxPage = $row['COUNT(id)'];
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
			
			$sql = "SELECT id FROM `images` WHERE queue = 0 " . $time. " ORDER BY id DESC LIMIT " . $offset . ", " . $this->lncln->display->settings['perpage'];
			$result = mysql_query($sql);
			
			$numRows = mysql_num_rows($result);
			
			for($i = 0; $i < $numRows; $i++){
				$row = mysql_fetch_assoc($result);
				
				$this->lncln->imagesToGet[] = $row['id'];
			}
		}
	}
}
?>
