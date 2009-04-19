<?
/**
 * queue.php
 * 
 * Exactly what it sounds like
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.11.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("../load.php");

$lncln->queue();

include("admin.php");

if($_GET['action'] == "update" && $lncln->user->permissions['isAdmin'] == 1){
	$lncln->dequeue($_POST);
	header("location:" . URL . "admin/" . $lncln->script);
	exit();
}

if(isset($_GET['delete'])){
	$output = $lncln->delete($_GET['delete']);
}

if(isset($_GET['obscene'])){
	$output = $lncln->obscene($_GET['obscene']);
}

if(isset($_GET['refresh'])){
	$id = prepareSQL($_GET['refresh']);
	
	$sql = "SELECT type FROM images WHERE id = " . $id;
	$result = mysql_query($sql);
	
	if(mysql_num_rows($result) == 1){
		$row = mysql_fetch_assoc($result);
		$lncln->thumbnail($id . "." . $row['type']);
	}
}

$lncln->img();

require_once("../includes/header.php");

$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
$result = mysql_query($sql);
$result = mysql_fetch_assoc($result);

if(isset($output)){
	echo $output . "<br />";
}

echo "There are " . $result['COUNT(*)'] . " items in the queue.";
?>
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
			<a href="queue.php?refresh=<?echo $image['id'];?>" ><img src="<?=URL;?>theme/<?echo THEME;?>/images/refresh.png" alt="Refresh" title="Refresh" style='border: none;'/></a>
			<a href="queue.php?delete=<? echo $image['id'];?>" ><img src="<?=URL;?>theme/<?=THEME;?>/images/delete.png" alt="Delete" title="Delete" style='border: none;'/></a><br />
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

require_once("../includes/footer.php");
?>