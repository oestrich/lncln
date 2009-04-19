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

if($_GET['action'] == "update"){
	$lncln->dequeue($_POST);
	header("location:" . URL . "admin/" . $lncln->script);
	exit();
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

<?
echo $lncln->prevNext();
?>
<form action="queue.php?action=update<?=$img;?>" method="post">
<?
	foreach($lncln->images as $image):	
		$tags = join(', ', $image['tags']);
?>
	<div id="<?=$image['id'];?>" class="modDiv">
		<input type="checkbox" name="check[<?=$image['id'];?>]" id="check<?=$image['id'];?>" /><br />
		Obscene: <select name="images[<?=$image['id'];?>][obscene]" onfocus="modCheck('<?=$image['id'];?>')">
				<?if($image['obscene'] == 1):?>
					<option value="1" selected>Yes</option><option value="0">No</option>
				<?else:?>
					<option value="1">Yes</option><option value="0" selected>No</option>
				<?endif;?>
				 </select>
		<a href="<?=URL;?>images/full/<?=$image['file'];?>" target="_blank" class="modImage"><img src="<?=URL;?>images/thumb/<?=$image['file'];?>" /></a>
		<div class="modForms">
			<input type="hidden" name="images[<?=$image['id'];?>][id]" value="<?=$image['id'];?>" /><br />
			Tags:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name='images[<?=$image['id'];?>][tags]' value="<?=$tags;?>" /><br />
			Caption:&nbsp;<textarea name="images[<?=$image['id'];?>][caption]" rows="10" cols="50" wrap="off" ><?=$image['caption'];?></textarea><br />
			Album:&nbsp;&nbsp;&nbsp;
			<select name="images[<?=$image['id'];?>][album]">
				<option value="0">No album</option>
	<?foreach($lncln->getAlbums() as $album):?>
		<?$selected = $album['name'] == $image['album'] ? "selected" : "";?>
				<option value="<?=$album['id'];?>" <?=$selected;?>><?=$album['name'];?></option>
	<?endforeach;?>
			</select>
		</div>
	</div>
<?
	endforeach;
?>
	<input type="submit" value="Submit" />
</form>
<div id='bPrevNext'>
<?
	echo $lncln->prevNext();
?>
</div>	
<?


require_once("../includes/footer.php");
?>