/**
 * javascript.js
 *
 * Main javascript file, included by the header
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

var formAction

function init(){
	formAction = document.getElementById('form').action;
}

function toggleDiv(type){
	if(document.getElementById('form').style.display == 'none'){
		document.getElementById('form').style.display = 'block';
	}else{
		if(type == document.getElementById('formType').value){
			document.getElementById('form').style.display = 'none';
		}
	}
	
	if(type == "url"){
		for(var a = 0; a < 10; a++){
			var upload = document.getElementById('upload' + a);
			upload.setAttribute('type','text');
			upload.setAttribute('size', 30);
		}
		document.getElementById('formType').value = "url";
		document.getElementById('form').action = formAction + "?url=true";
	}
	else{
		for(var a = 0; a < 10; a++){
			var upload = document.getElementById('upload' + a);
			upload.setAttribute('type','file');
			upload.setAttribute('size', 20);
		}
		document.getElementById('formType').value = "regular";
		document.getElementById('form').action = formAction;
	}
}

function obscene(divid){
	document.getElementById('i' + divid).style.display = 'block';
	document.getElementById('l' + divid).href = "image.php?img=" + divid;
}

function badImage(divid){
	document.getElementById('b' + divid).style.display = 'block';
	document.getElementById('l' + divid).href = "image.php?img=" + divid;
}

function both(divid){
	document.getElementById('b' + divid).style.display = 'block';
	document.getElementById('i' + divid).style.display = 'block';
	document.getElementById('l' + divid).href = "image.php?img=" + divid;
}

function TestFileType( fileName, fileTypes ) {
	if (!fileName) return;

	dots = fileName.split(".")
	//get the part AFTER the LAST period.
	fileType = "." + dots[dots.length-1];

	return (fileTypes.join(".").indexOf(fileType) != -1) ? 1 : alert("Please only upload files that end in types: \n" + (fileTypes.join(" .")) + "\n\nNothing will be done with these files.\n\nPlease upload a valid image.");
}

function caption(divid){
	document.getElementById('c' + divid).style.display = 'block';
	document.getElementById('formCaption' + divid).focus();
	document.getElementById('caption' + divid).style.display = 'none';
}

function tag(divid){
	document.getElementById('t' + divid).style.display = 'block';
	document.getElementById('formTag' + divid).focus();
	document.getElementById('tag' + divid).style.display = 'none';
}

function album(divid){
	document.getElementById('a' + divid).style.display = 'block';
	document.getElementById('album' + divid).style.display = 'none';
}

function modCheck(divid){
	document.getElementById('check' + divid).checked = true;
}

function queueCheck(divid){
	document.getElementById('check' + divid).value = 1;
}