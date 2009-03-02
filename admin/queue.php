<?
/**
 * queue.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.8.0 $Id$
 * @license license.txt GNU General Public License
 * 
 * @package lncln
 */

require_once("../load.php");

$lncln = new lncln("queue");
$lncln->loggedIn();


if($lncln->isAdmin){
	$lncln->queue = true;
}

if($_GET['action'] == "update"){
	$lncln->dequeue($_POST);
	header("location:" . URL . "admin/" . $lncln->script);
	exit();
}

if(isset($_GET['delete']) && $lncln->isAdmin){
	$deletion = $lncln->delete($_GET['delete']);
}

if(isset($_GET['obscene']) && $lncln->isAdmin){
	$obscene = $lncln->obscene($_GET['obscene']);
}

$lncln->img();

require_once("../includes/header.php");

$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
$result = mysql_query($sql);
$result = mysql_fetch_assoc($result);

if(isset($deletion)){
	echo $deletion . "<br />";
}
if(isset($obscene)){
	echo $obscene . "<br />";
}


echo "There are " . $result['COUNT(*)'] . " items in the queue.";

if($lncln->isAdmin){
?>
	<script type="text/javascipt">
		function check(id){
			
		}
	</script>
	<br />
	This page will show the first 50 items in the queue<br />
	<form enctype="multipart/form-data" action="queue.php?action=update" method="post">
		<div class="queue">
<?	
	if(count($lncln->images) > 0){
		foreach ($lncln->images as $image){
			if($image['obscene'] == 1){
				$obscene = "obscene";
			}
			else{
				$obscene = "not obscene";
			}
			
			$tags = join(', ', $image['tags']);
			if($tags == ""){
				$tags = "None.";
			}
?>
		
			<input type='checkbox' name='<?echo $image['id'];?>' value='<?echo $image['id'];?>' style="float: left;" />
			<div class="imageLink">
				<a name="<?echo $image['id'];?>" href="<?=URL;?>images/full/<?echo $image['file'];?>" target="_blank"><img src="<?=URL;?>images/thumb/<?echo $image['file'];?>" alt="<?echo $image['id'];?>" /></a><br />
				<a href="queue.php?obscene=<? echo $image['id'];?>" class="delete"><img src="<?=URL;?>theme/<?=THEME;?>/images/obscene.png" alt="Obscene" title="Obscene" style='border: none;'/></a>
				<a href="queue.php?delete=<? echo $image['id'];?>"><img src="<?=URL;?>theme/<?=THEME;?>/images/delete.png" alt="Delete" title="Delete" style='border: none;'/></a><br />
				<div class='delete'>Tags: <?echo $tags;?></div>
				<div class='delete'>This image is <?echo $obscene;?>.</div>
			</div>

<?		}
?>
			<input type='submit' value='Submit' />
		</div>
	</form>
<?
	}
	else{
?>
	<br />Nothing to moderate
<?
	}
}
else{
	echo "You're not an admin, please go back to the <a href='index.php'>main page</a>";
}

require_once("../includes/footer.php");
?>