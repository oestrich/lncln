<?
/**
 * moderate.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.9.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */ 

require_once("../load.php");

$lncln = new lncln("index");

require_once(ABSPATH . "includes/iconActions.php");

$lncln->img();


if($lncln->isAdmin):
	foreach($lncln->images as $image):	
	?>
		<div id="<?=$image['id'];?>">
			<a href="<?=URL;?>images/full/<?$image['file'];?>" target="_blank"><img src="<?URL;?>images/thumb/<?=$image['file'];?>" /></a>
		</div>
	<?
	endforeach;
endif;
?>
