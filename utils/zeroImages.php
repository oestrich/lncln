<?
/*
/	lncln by Eric Oestrich
/	Version .5
/
*/

function padZero($curdir){
	$files = scandir($curdir);
	
	$nextNum = str_pad(2, 6, 0, STR_PAD_LEFT);
	
	for($i = 2; $i < count($files) - 1; $i++){
		//gets the type
        $typeTmp = split("\.", $files[$i]);
		//this is the type
        $type = $typeTmp[count($typeTmp) - 1];
		
		rename($curdir . $files[$i], $curdir . $nextNum . '.' . $type);
		
		$nextNum = str_pad($nextNum + 1, 6, 0, STR_PAD_LEFT);
	}
}

padZero("img/");
padZero("normal/");
padZero("thumb/");

?>