<?php
/**
 * header.php
 * 
 * This should be included in every file before output is to be sent.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title><?echo $lncln->display->title;?></title>
<?
	foreach($lncln->modules as $module){
		if(method_exists($module, "html_head")){
			$module->html_head();
		}
	}
		echo $lncln->display->include_css();
	?>
	<script type="text/javascript" src="<?echo URL;?>includes/lncln.js" >
	</script>
</head>
<body onload="init()">
	<form action="#" method="post">
		<div><input type="hidden" id="URL" value="<?=URL;?>" /></div>
	</form>
	<div id="container">
		<div id="header">
			<a href="<?echo URL;?>index/" ><img src="<?echo URL;?>theme/<?echo THEME;?>/images/abe.png" alt="Abe" id="abeLink" /></a>
			<div id="navBar">
			
<?

$lncln->display->show_header_links();

?>
			</div>
		</div>
		<div id="mainBody">
			<br />
