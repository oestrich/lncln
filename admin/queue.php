<?
/**
 * queue.php
 * 
 * Exactly what it sounds like
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

require_once("../load.php");

$lncln->queue();

include_once("admin.php");

if($_GET['action'] == "update"){
	foreach($_POST['check'] as $key => $value){
		foreach($lncln->modules as $modKey => $module){
			$module->edit($key, array($_POST['images'][$key][$modKey]));
		}
		$lncln->caption($key, $_POST['images'][$key]['caption']);
		$lncln->obscene($key, $_POST['images'][$key]['obscene']);
	}
	
	foreach($_POST['approve'] as $key => $value){
		$lncln->dequeue($key);
	}
}

if($_GET['action'] == "delete" && is_array($_POST['approve'])){
	foreach($_POST['approve'] as $key => $value){
		$lncln->delete($key);
	}
}

if($_GET['action'] == "update"){
	$lncln->dequeue($_POST);
	header("location:" . URL . "admin/" . $lncln->script);
	exit();
}

$lncln->img();

include_once(ABSPATH . "includes/header.php");

$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
$result = mysql_query($sql);
$result = mysql_fetch_assoc($result);

echo "There are " . $result['COUNT(*)'] . " items in the queue.";
?>
<br /><br/>
<?
echo $lncln->prevNext();
?>
<form id='queue' action="queue.php?action=update" method="post">
<?
	foreach($lncln->images as $image):
?>
	<div id="<?=$image['id'];?>" class="modDiv">
		<input type="hidden" name="check[<?=$image['id'];?>]" id="check<?=$image['id'];?>" value="0" />
		Approve: <input type="checkbox" name="approve[<?=$image['id'];?>]" id="approve<?=$image['id'];?>" style="height: 35px; width: 35px;" /><br />
		Obscene: <select name="images[<?=$image['id'];?>][obscene]" >
				<?if($image['obscene'] == 1):?>
					<option value="1" selected>Yes</option><option value="0">No</option>
				<?else:?>
					<option value="1">Yes</option><option value="0" selected>No</option>
				<?endif;?>
				 </select>
		<a href="<?=URL;?>images/full/<?=$image['file'];?>" target="_blank" class="modImage"><img src="<?=URL;?>images/thumb/<?=$image['file'];?>" /></a>
		<div class="modForms">
			<input type="hidden" name="images[<?=$image['id'];?>][id]" value="<?=$image['id'];?>" /><br />
			<table>
				<?foreach($lncln->modules as $module):
					if($module->moderate($image['id']) == "")
						continue;
				?>
				<tr>
					<td><?=$module->displayName;?>:</td>
					<td><?=createInput($module->moderate($image['id']), $image['id'], " onfocus=\"modCheck('" . $image['id'] . "')\" ");?></td>
				</tr>
				<?endforeach;?>
			</table>
		</div>
	</div>
<?
	endforeach;
?>
	<input type="submit" value="Submit" />
	<input type="submit" value="Delete Selected" onclick="document.getElementById('queue').action = 'queue.php?action=delete';" />
</form>
<form action="queue.php?action=update" method="post">
	<div>
	<?foreach($lncln->images as $image):?>
		<input type="hidden" name="approve[<?=$image['id'];?>]" value="1" />
	<?endforeach;?>
		<input type="submit" value="Approve All" />
	</div>
</form>
<div id='bPrevNext'>
<?
	echo $lncln->prevNext();
?>
</div>	
<?


include_once(ABSPATH . "includes/footer.php");
?>