<?
/*
/	lncln by Eric Oestrich
/	Version .5
/
*/

function thumbnail($img, $curURL){

	$size = getimagesize("img/" . $img);
	
	$tHeight = ($size[1] / $size[0]) * 150;
	
	if($size[1] > 600 || $size[0] > 600){
		$norm = "&w=600";
	}

	if($tHeight > 150){
		$thumb = "&h=150";
	}else{
		$thumb = "&w=150";
	}


	$curServer = "http://" . $_SERVER['SERVER_NAME'] . "/";
	$get = $curServer . "lib/phpThumb.php?src=". $curURL . $img . "&q=35";
	echo $get . $thumb;
	echo "<br / >";
	echo $get . $norm;
	file_put_contents('thumb/' . $img, file_get_contents($get . $thumb));
	file_put_contents('normal/' . $img, file_get_contents($get . $norm));
}

$curURL = str_replace("thumbCreator.php", "", $_SERVER['SCRIPT_NAME']) . "img/";

$files = scandir("img/");

for ($i = 2; $i <= count($files); $i++){
	thumbnail($files[$i], $curURL);
}

?>