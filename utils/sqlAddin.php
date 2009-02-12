<?
/**
 * lncln by Eric Oestrich
 * version 0.6.0
 * 
 * @package lncln
 */

require_once("config.php");

function sqlAdd($config, $curdir){
	mysql_connect($config['server'], $config['user'], $config['password']) or die(mysql_error());
	mysql_select_db($config['database']) or die(mysql_error());
	
	$files = scandir($curdir);
	
	for($i = 2; $i < count($files) - 1; $i++){
		//gets the type
        $typeTmp = split("\.", $files[$i]);
		//this is the type
        $type = $typeTmp[count($typeTmp) - 1];
		
		$sql = "INSERT INTO images (type) VALUES('" . $type . "')";
		mysql_query($sql);
		
		echo "Inserted " . $files[$i];
		echo "<br />";
		echo $sql;
		echo "<br />";
	}
}

sqlAdd($config['mysql'], "img/");

?>