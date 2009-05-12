<?
/**
 * moderate.php
 * 
 * Easily moderate many images at a time.  Bulk editing has never been easier!
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */ 

require_once("../load.php");

$lncln->index();

include_once("admin.php");

if($_GET['action'] == "update"){
	if(!is_array($_POST['check']))
		$_POST['check'] = array();
	
	foreach($_POST['check'] as $key => $value){
		foreach($lncln->modules as $modKey => $module){
			$module->edit($key, array($_POST['images'][$key][$modKey]));
		}
		$lncln->obscene($key, $_POST['images'][$key]['obscene']);
	}
}

$lncln->img();

include_once(ABSPATH . "includes/header.php");

echo $lncln->prevNext();

$img = $_GET['img'] != '' ? "&amp;img=" . $_GET['img'] : "";
 
?>
<form action="moderate.php?action=update<?=$img;?>" method="post">
<?
	foreach($lncln->images as $image):
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
</form>
<div id='bPrevNext'>
<?
	echo $lncln->prevNext();
?>
</div>	
<?


include_once(ABSPATH . "includes/footer.php");

?>
