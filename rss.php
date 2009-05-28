<?
/**
 * rss.php
 * 
 * Shows the rss
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

header('Content-type: text/xml'); 

/**
 * Getting the page started
 */
require_once("load.php");

$action = $_GET['action'];
if($action == "")
	$action = "safe";

$lncln->rss(array($action));
$lncln->img();

$server = "http://" . $_SERVER['SERVER_NAME'] . str_replace("rss.php", "", $_SERVER['SCRIPT_NAME']);

?>
<rss version="2.0">
<channel>

<title>The Archive</title>
<link><?echo $server;?></link>
<description>All Images</description>
<lastBuildDate>Mon, 26 Jan 2009 18:37:00 EST</lastBuildDate>
<language>en-us</language>

<?
foreach($lncln->images as $image){
?>
	<item>
		<title><?echo $image['id'];?></title>
		<link><?echo $server;?>image/<?echo $image['id'];?></link>
		<guid><?echo $server;?>image/<?echo $image['id'];?></guid>
		<pubDate><?echo date('r', $image['postTime']);?></pubDate>
		<description>
			<![CDATA[ <img src="<?=$lncln->getImagePath($image['id'], "index");?>" alt="<?echo $image['id'];?>"/> <br />
			<a href="<?=$lncln->getImagePath($image['id'], "full");?>" />Larger</a><br />
			<?foreach($lncln->modules as $module):
				if(method_exists($module, "rss")){
					$output = $module->rss($image['id']);
					if($output != "")
						echo $output . "\n\t\t\t<br/>\n\t\t\t";
				}
			endforeach;?>]]>
		</description>
	</item>
<?
}
?>

</channel>
</rss>
