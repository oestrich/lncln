<?
/**
 * queue.php
 * 
 * @author Eric Oestrich
 * @version 0.6.0
 * 
 * @package lncln
 */

require_once("config.php");
require_once("functions.php");

connect($config['mysql']);
list($isLoggedIn, $isAdmin) = loggedIn();

if($isAdmin){
	$queue = true;
}
else{
	$queue = false;
}

if(isset($_POST)){
	dequeue($_POST);
}

if(isset($_GET['delete'])){
	$deletion = delete($_GET['delete']);
}

if(isset($_GET['obscene']) && $isLoggedIn){
	$obscene = obscene($_GET['obscene']);
}

list($start, $prev, $next, $numImgs) = init();

list($images, $type) = img($start, $queue, $isAdmin);

$sql = "SELECT COUNT(*) FROM images WHERE queue = 1";
$result = mysql_query($sql);
$result = mysql_fetch_assoc($result);

require_once("header.php");

if(isset($deletion)){
	echo $deletion . "<br />";
}
if(isset($obscene)){
	echo $obscene . "<br />";
}


echo "There are " . $result['COUNT(*)'] . " items in the queue.";

if($isAdmin){
?>
	<script type="text/javascipt">
		function check(id){
			
		}
	</script>
	<br />
	This page will show the first 50 items in the queue<br />
	<form enctype="multipart/form-data" action="queue.php" method="post">
		<div class="queue">
<?	
	if(count($images) > 0){
		foreach ($images as $image){
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
				<a name="<?echo $image['id'];?>" href="img/<?echo $image['file'];?>"><img src="thumb/<?echo $image['file'];?>" alt="<?echo $image['id'];?>" /></a><br />
				<a href="queue.php?obscene=<? echo $image['id'];?>" class="delete"><img src="images/obscene.png" alt="Obscene" title="Obscene" style='border: none;'/></a>
				<a href="queue.php?delete=<? echo $image['id'];?>"><img src="images/delete.png" alt="Delete" title="Delete" style='border: none;'/></a><br />
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
	else{?>
	<br />Nothing to moderate
	<?}
}
else{
	echo "You're not an admin, please go back to the <a href='index.php'>main page</a>";
}

require_once("footer.php");
?>