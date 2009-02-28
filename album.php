<?php
/**
 * album.php
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.8.0 $Id$
 * @license license.txt GNU General Public License
 * 
 * @package lncln
 */

require_once("load.php");

$lncln = new lncln("album");
$lncln->loggedIn();

foreach($lncln->getAlbums() as $album):?>
	<a href="<?=$lncln->script;?>?album=<?=$album['id'];?>"><?=$album['name'];?></a>
<?endforeach;

?>
