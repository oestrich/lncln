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

var formAction;
var URL;

function init(){
	formAction = document.getElementById('form').action;
	URL = document.getElementById('URL').value;
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
			var temp = document.getElementById('upload' + a);
			var upload = document.createElement('input');
			
			upload.setAttribute('type','text');
			upload.setAttribute('size', 30);
			upload.setAttribute('id', 'upload' + a);
			upload.setAttribute('name', 'upload' + a);
			
			temp.parentNode.replaceChild(upload, temp);
		}
		document.getElementById('formType').value = "url";
		document.getElementById('form').action = formAction + "?url=true";
	}
	else{
		for(var a = 0; a < 10; a++){
			var temp = document.getElementById('upload' + a);
			var upload = document.createElement('input');
			
			upload.setAttribute('type','file');
			upload.setAttribute('size', 20);
			upload.setAttribute('id', 'upload' + a);
			upload.setAttribute('name', 'upload' + a);
			
			temp.parentNode.replaceChild(upload, temp);
		}
		document.getElementById('formType').value = "regular";
		document.getElementById('form').action = formAction;
	}
}

function badImage(divid){
	document.getElementById('b' + divid).style.display = 'block';
	document.getElementById('l' + divid).href = URL + "image.php?img=" + divid;
}

function showModule(module, divid){
	document.getElementById(module.charAt(0).toLowerCase() + divid).style.display = 'block';
	document.getElementById('form' + module + divid).focus();
	document.getElementById(module.toLowerCase() + divid).style.display = 'none';
}

function news(){
	var actualNews = document.getElementById('actualNews');
	if(actualNews.style.display == "block"){
		actualNews.style.display = "none";
	}
	else{
		actualNews.style.display = "block";
	}
}

function modCheck(divid){
	document.getElementById('check' + divid).checked = true;
}

function queueCheck(divid){
	document.getElementById('check' + divid).value = 1;
}