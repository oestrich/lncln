<?
/*
/	lncln by Eric Oestrich
/	Version .5
/
*/

function connect($config){
	mysql_connect($config['server'], $config['user'], $config['password']);
	mysql_select_db($config['database']);
}

function init(){
	$result = mysql_query("SELECT MAX(id) FROM images");
	$result = mysql_fetch_assoc($result);
	
	$numImgs = $result['MAX(id)'];
	
	if(!isset($_GET['img'])){
		$start = $numImgs;
	}else{
		//if it's set, then set it to start
		$start = $_GET['img'];
		if($start == ""){
			$start = $numImgs;
		}
		//incase its to large
		if($start > $numImgs){
			$start = $numImgs;
		}
	}
	
	//Getting the number to start the next page
	$sql = "SELECT id FROM `images` WHERE id <= " . $start . " AND queue = 0 ORDER BY id DESC LIMIT 51";
	$result = mysql_query($sql);
	
	$numRows = mysql_num_rows($result);
	mysql_data_seek($result, $numRows - 1);
	$row = mysql_fetch_assoc($result);
	$next = $row['id'];
	
	//getting the prevsion page
	$sql = "SELECT id FROM `images` WHERE id > " . $start . " AND queue = 0 ORDER BY id ASC LIMIT 50";
	$result = mysql_query($sql);
	
	$numRows = mysql_num_rows($result);
	if($numRows > 0){
		mysql_data_seek($result, $numRows - 1);
		$row = mysql_fetch_assoc($result);	
		$prev = $row['id'];
	}
	else{
		$prev = $start;
	}
	
	return array($start, $prev, $next, $numImgs);
}

function thumbnail($img){
	$size = getimagesize("img/" . $img);
	
	$type = split("\.", $img);
	$type = $type[count($type) - 1];
	
	$tHeight = ($size[1] / $size[0]) * 150;

	if($size[1] > 600 || $size[0] > 600){
		$norm = "600x" . $size[1];
	}
	else{
		$norm = $size[0] . "x" . $size[1];
	}

	if($tHeight > 150){
		$thumb =  $size[0] . "x150";
	}else{
		$thumb = "150x" . $size[1];
	}

	if($type == "gif"){
		$command = "convert -resize '" . $thumb . "' -quality 35 img/" . $img . "[0] thumb/" . $img . ".jpg";
		exec($command);
		
		$command = "convert thumb/" . $img . ".jpg thumb/" . $img;
	}
	else{
		$command = "convert -resize '" . $thumb . "' -quality 35 img/" . $img . " thumb/" . $img;
	}
	exec($command);
	
	if($type == "gif"){
		$command = "convert -resize '" . $norm . "' -quality 35 img/" . $img . "[0] normal/" . $img . ".jpg";		
		exec($command);
		
		$command = "convert normal/" . $img . ".jpg normal/" . $img;
	}
	else{
		$command = "convert -resize '" . $norm . "' -quality 35 img/" . $img . " normal/" . $img;
	}
	exec($command);
	
	if($type == "gif"){
		unlink("normal/" . $img . ".jpg");
		unlink("thumb/" . $img . ".jpg");
	}
}

function img($start, $queue, $isAdmin, $search = ""){
	$images = array();
	
	if($queue){
		$sql = "SELECT id, caption, postTime, type, obscene, rating FROM images WHERE queue = 1 ORDER BY `id` ASC LIMIT 50";
	}
	else if($search != ""){
		$search = stripslashes($search);
		$search = mysql_real_escape_string($search);
		$sql = "SELECT picId FROM tags WHERE tag LIKE '%" . $search . "%'";
		$result = mysql_query($sql);
		
		$sql = "SELECT id, caption, postTime, type, obscene, rating FROM images WHERE queue = 0 AND ( ";
		
		while($row = mysql_fetch_assoc($result)){
			$sql .= "id = " . $row['picId'] . " OR ";
		}
	
		$sql = substr_replace($sql ,"",-3);
		$sql .= ") AND postTime <= " . time() . " ORDER BY id DESC";
	}
	else{
		if($isAdmin != true){
			$time = "AND postTime <= " . time();
		}
		$sql = "SELECT id, caption, postTime, type, obscene, rating FROM images WHERE queue = 0 AND id <= " . $start . " " . $time . " ORDER BY `id` DESC LIMIT 50";
	}
	
	$result = mysql_query($sql);
	$numRows = @mysql_num_rows($result);
	
	for($i = $end; $i < ($end + $numRows); $i++){
		$image = mysql_fetch_assoc($result);
		
		$sql = "SELECT tag FROM tags WHERE picId = " . $image['id'];
		$tags = mysql_query($sql);
		
		$imageTags = array();
		
		while($tag = mysql_fetch_assoc($tags)){
			$imageTags[] = $tag['tag'];
		}
		
		$images[$i] = array(
			'id' 		=> $image['id'],
			'file' 		=> $image['id'] . "." . $image['type'],
			'type'		=> $image['type'],
			'obscene' 	=> $image['obscene'],
			'rating' 	=> $image['rating'],
			'postTime'	=> $image['postTime'],
			'caption'	=> $image['caption'],
			'tags' 		=> $imageTags
			);
	}
	
	//$sql = " SELECT SUM( upDown ) AS rating FROM rating WHERE picId = " . $
	
	$type = "normal";
	$extra = "";
	
	if($_GET['thumb']){
		$type = "thumb";
		$extra = "&amp;thumb=true";
	}
	
	return array($images, $type, $extra);
}

function upload($numImgs, $curdir, $curURL){
	$_SESSION['uploaded'] = true;
	$_SESSION['pages'] = 0;
	
	for($i = 0; $i < 10; $i++){
		$sql = "SELECT MAX(postTime) FROM images";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		if(time() >= ($row['MAX(postTime)'] + (60 * 15))){
			$postTime = time();
		}
		else{
			$postTime = $row['MAX(postTime)'] + (60 * 15);
		}
		
		//if nothing in either style uploads
		if($_POST['upload' . $i] == "" && $_FILES['upload'.$i]['name'] == ""){
			$_SESSION['upload'][$i] = 0;
			continue;
		}
		
		if($_GET['url']){
			$typeTmp = split("\.", $_POST['upload' . $i]);
		}
		else{
			//splits the upload name to get the file extension
			$typeTmp = split("\.", $_FILES['upload'.$i]['name']);
		}
		
        //the file extension
		$type = $typeTmp[count($typeTmp) - 1];
		
		if($_POST['upload' . $i . 'check']){
			$obscene = 1;
		}
		else{
			$obscene = 0;
		}
        
		if($_POST['upload' . $i . 'tags'] == ""){
			$_SESSION['upload'][$i] = 3;
			continue;
		}
		
		//only these types
        if($type == "png" || $type == "jpg" || $type == "gif"){
			$_SESSION['upload'][$i] = 2;
			if($_GET['url']){
				$file = @file_get_contents($_POST['upload' . $i]);
				if(!$file){
					$_SESSION['upload'][$i] = 5;
					continue;
				}
			}
			
			if (isset($_COOKIE['username'])){
				$sql = "SELECT numImages, postTime, admin FROM users WHERE name = '" . $_COOKIE['username'] . "'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				
				if(date('d', $row['postTime']) != date('d', time()) && !$row['admin']){
					$sql = "UPDATE users SET postTime = " . time() . ", numImages = 1 WHERE name = '" . $_COOKIE['username'] . "' LIMIT 1"; 
					mysql_query($sql);
										
					$sql = "INSERT INTO images (postTime, type, queue, obscene) VALUES (" . $postTime . ", '" . $type . "', 0, " . $obscene . ")";
					$_SESSION['upload'][$i] = 1;
				}				
				else if($row['numImages'] >= 20 && date('d', $row['postTime']) == date('d', time()) && !$row['admin']){
					$sql = "UPDATE users SET postTime = " . time() . ", numImages = " . ($row['numImages'] + 1) . " WHERE name = '" . $_COOKIE['username'] . "' LIMIT 1"; 
					mysql_query($sql);
					
					$sql = "INSERT INTO images (postTime, type, obscene) VALUES (" . $postTime . ", '" . $type . "', " . $obscene . ")";
					$_SESSION['upload'][$i] = 2;
				}
				else if($row['numImages'] < 20 && !$row['admin']){
					$sql = "UPDATE users SET postTime = " . time() . ", numImages = " . ($row['numImages'] + 1) . " WHERE name = '" . $_COOKIE['username'] . "' LIMIT 1"; 
					mysql_query($sql);
					
					$sql = "INSERT INTO images (postTime, type, queue, obscene) VALUES (" . $postTime . ", '" . $type . "', 0, " . $obscene . ")";
					$_SESSION['upload'][$i] = 1;
				}
				else if($row['admin']){
					$sql = "INSERT INTO images (postTime, type, queue, obscene) VALUES (" . $postTime . ", '" . $type . "', 0, " . $obscene . ")";
					$_SESSION['upload'][$i] = 1;
				}
				else{
					$sql = "INSERT INTO images (postTime, type, obscene) VALUES (" . $postTime . ", '" . $type . "', " . $obscene . ")";
					$_SESSION['upload'][$i] = 2;
				}
			}
			else{
				$sql = "INSERT INTO images (postTime, type, obscene) VALUES (" . $postTime . ", '" . $type . "', " . $obscene . ")";
				$_SESSION['upload'][$i] = 2;
			}
			
			$_SESSION['uploadTime'][$i] = $postTime;
			
			mysql_query($sql);
			
			$imgID = str_pad(mysql_insert_id(), 6, 0, STR_PAD_LEFT);
			
			$_SESSION['image'][$i] = $imgID . '.' . $type;
			
			if($_GET['url']){
				file_put_contents($curdir . $imgID . '.' . $type, $file);
			}
			else{
				//moves the files
				move_uploaded_file($_FILES['upload'.$i]['tmp_name'], $curdir . $imgID . '.' . $type);
			}
			
			thumbnail($imgID . '.' . $type);
			tag($imgID, $_POST['upload' . $i . 'tags']);
        }
		else{
			$_SESSION['upload'][$i] == 4;
		}
	}
}
/*
function scan($curdir){
	//the upload directory
    $dir = getcwd() . "/upload/";
	
    $files = scandir($dir);
	
	if (count($files) < 3){
		return "Nothing to upload";
	}
	
    //gets the next number in the directory
	$nextNum = str_pad(count($files) - 1, 6, 0, STR_PAD_LEFT);
	
	for($i = 2; $i < count($files); $i++){
		//gets the type
        $typeTmp = split("\.", $files[$i]);
		//this is the type
        $type = $typeTmp[count($typeTmp) - 1];
        
		//only allows these
        if($type == "png" || $type == "jpg" || $type == "gif"){
			//moves the pictures
            rename($dir.$files[$i], $curdir . $nextNum . '.' . $type);
			//gets the next number
            $nextNum = str_pad($nextNum + 1, 6, 0, STR_PAD_LEFT);
			//prints what number it uploaded
            //print "uploaded " . $i . "<br />";
        }
	}
	return "Uploaded " . (count($files) - 2);
}
*/
function prevNext($start, $prev, $next, $numImgs, $type){
	if ($type == thumb){
		$thumb = "&amp;thumb=true";
	}else{
		$thumb = "";
	}
	
	if ($start == $numImgs){
        return "<a href='index.php?img=" . $next . $thumb . "' class='prevNext'>Next 50</a>";
    }elseif($next == 1){
        return "<a href='index.php?img=" . $prev . $thumb . "' class='prevNext'>Prev 50</a>";
    }else{
        return "<a href='index.php?img=" . $prev . $thumb . "' class='prevNext'>Prev 50</a>
        <a href='index.php?img=" . $next . $thumb . "' class='prevNext'>Next 50</a>";
    }
	return "";
}

function loggedIn(){
	$isAdmin = false;
	
	if(isset($_COOKIE['password']) && isset($_COOKIE['username'])){
		$username = stripslashes($_COOKIE['username']);
		$password = stripslashes($_COOKIE['password']);

		$username = mysql_real_escape_string($username);
		$password = mysql_real_escape_string($password);

		$sql = "SELECT * FROM users WHERE name = '" . $username . "' AND password = '" . $password . "'";

		$result = mysql_query($sql);
		$numRows = mysql_num_rows($result);

		if($numRows == 1){
			$result = mysql_fetch_assoc($result);
			
			if($result['admin'] == 1){
				$isAdmin = true;
			}
			
			$isLoggedIn = true;
			$userID = $result['id'];
		}
		else{
			$isLoggedIn = false;
		}
	}
	else{
		if(!isset($_COOKIE['password']) && $_COOKIE['username']){
			setcookie("username", $username, time() - (60 * 60 * 24));
			setcookie("password", $password, time() - (60 * 60 * 24));
			header("location:index.php");
		}
		$isLoggedIn = false;
	}
	
	return array($isLoggedIn, $isAdmin, $userID);
}

function dequeue($images){
	$numImages = count($images);
	
	foreach($images as $image){
		$sql = "UPDATE images SET queue = 0, report = 0 WHERE id = " . $image . " LIMIT 1";
		mysql_query($sql);
	}
}

function adduser($user){
	$username = stripslashes($user['username']);
	$password = stripslashes($user['password']);
	$passwordConfirm = stripslashes($user['passwordconfirm']);
	$admin = stripslashes($user['admin']);

	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string($password);
	$passwordConfirm = mysql_real_escape_string($passwordConfirm);
	$admin = mysql_real_escape_string($admin);
	
	$password = sha1($password);
	$passwordConfirm = sha1($passwordConfirm);
	
	if($password != $passwordConfirm){
		return "Passwords do not match";
	}
	
	$sql = "INSERT INTO users (name, password, admin) VALUES ('" . $username . "', '" . $password . "', " . $admin . ")";
	mysql_query($sql);
	
	return "User " . $username . " added";
}

function updateUser($user){
	$username = stripslashes($user['username']);
	$obscene = stripslashes($user['obscene']);

	$username = mysql_real_escape_string($username);
	$obscene = mysql_real_escape_string($obscene);
	
	if($user['password'] != "" && $user['newPassword'] != "" && $user['newPasswordConfirm'] != ""){
		$oldPassword = stripslashes($user['password']);
		$newPassword = stripslashes($user['newPassword']);
		$newPasswordConfirm = stripslashes($user['newPasswordConfirm']);
		
		$oldPassword = mysql_real_escape_string($oldPassword);
		$newPassword = mysql_real_escape_string($newPassword);
		$newPasswordConfirm = mysql_real_escape_string($newPasswordConfirm);
		
		$sql = "SELECT password FROM users WHERE name = '" . $username . "' LIMIT 1";
		$result = mysql_query($sql);
		
		$row = mysql_fetch_assoc($result);
		
		$oldPassword = sha1($oldPassword);
		$newPassword = sha1($newPassword);
		$newPasswordConfirm = sha1($newPasswordConfirm);
		
		if($newPassword != $newPasswordConfirm || $oldPassword != $row['password']){
			return "Passwords do not match";
		}
		
		$password = "password = '" . $newPassword . "',";
		
		setcookie("password", $newPassword, time() + (60 * 60 * 24));
	}
	
	if($_POST['viewObscene']){
		$obscene = 1;
	}
	else{
		$obscene = 0;
	}
	
	
	$sql = "UPDATE users SET " . $password . " obscene = " . $obscene . " WHERE name = '" . $username . "' LIMIT 1";
	mysql_query($sql);
	
	setcookie("username", $username, time() + (60 * 60 * 24));
	setcookie('obscene', $obscene, time() + (60 * 60 * 24));

	
	return "User " . $username . " updated";
}

function delete($image){
	$sql = "SELECT type FROM images WHERE id = " . $image;
	$result = mysql_query($sql);
	if(mysql_num_rows($result) == 1){
		$type = mysql_fetch_assoc($result);
	}
	else{
		return "No such image.";
	}

	$sql = "DELETE FROM images WHERE id = " . $image . " LIMIT 1";
	mysql_query($sql);
	
	unlink("img/" . $image . "." . $type['type']);
	unlink("thumb/" . $image . "." . $type['type']);
	unlink("normal/" . $image . "." . $type['type']);
	
	return "Successfully deleted.";
}

function obscene($image){
	$sql = "SELECT type, obscene FROM images WHERE id = " . $image;
	
	$result = mysql_query($sql);
	if(mysql_num_rows($result) == 1){
		$row = mysql_fetch_assoc($result);
		switch($row['obscene']){
			case 0:
				$num = 1;
				break;
			case 1:
				$num = 0;
		}
	}
	else{
		return "No such image.";
	}
	
	$sql = "UPDATE images SET obscene = " . $num . " WHERE id = " . $image;
	
	mysql_query($sql);
	
	return "Updated image";
}

function rate($image, $user, $rating){
	//gets rating if they already rated image
	$sql = "SELECT upDown FROM rating WHERE picId = " . $image . " AND userId = " . $user;
	$result = mysql_query($sql);
	$numRows = mysql_num_rows($result);
	
	if($numRows > 0){
		$row = mysql_fetch_assoc($result);
	}
	
	if($numRows == 1 && $row['upDown'] == $rating){
		return "You already rated it";
	}
	else if(($numRows == 1 && $row['upDown'] != $rating) || $numRows == 0){
		if(isset($row['upDown']) && $row['upDown'] != $rating){
			$sql = "UPDATE rating SET upDown = " . $rating . " WHERE picId = " . $image . " AND userID = " . $user . " LIMIT 1";
		}
		else{
			$sql = "INSERT INTO rating (picID, userId, upDown) VALUES (" . $image . ", " . $user . ", " . $rating . ")";
		}
		mysql_query($sql);
		
		//gets current rating
		$sql = "SELECT SUM(upDown) FROM rating WHERE picId = " . $image;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		
		//sets the rating to the image
		$sql = "UPDATE images SET rating = " . $row['SUM(upDown)'] . " WHERE id = " . $image . " LIMIT 1";
		mysql_query($sql);
	}
	else if($numRows > 0){
		return "You already rated it";
	}
}

function caption($id, $caption){
	$id = stripslashes($id);
	$caption = stripslashes($caption);

	$id = mysql_real_escape_string($id);
	$caption = mysql_real_escape_string($caption);
	
	$sql = "UPDATE images SET caption = '" . $caption . "' WHERE id = " . $id . " LIMIT 1";
	mysql_query($sql);
}

function tag($id, $tags){
	$id = stripslashes($id);
	$id = mysql_real_escape_string($id);
	
	$tags = split(',', $tags);
	$tags = array_map('trim', $tags);
	$tags = array_map('stripslashes', $tags);
	$tags = array_map('mysql_real_escape_string', $tags);
	
	$sql = "DELETE FROM tags WHERE picId = " . $id;
	mysql_query($sql);
	
	$sql = "INSERT INTO tags (picId, tag) VALUES ";
	
	foreach($tags as $tag){
		if($tag == ""){
			continue;
		}
		$sql .= "(" . $id . ", '" . $tag . "'), ";
	}

	$sql = substr_replace($sql ,"",-2);
	
	mysql_query($sql);
	
}

?>