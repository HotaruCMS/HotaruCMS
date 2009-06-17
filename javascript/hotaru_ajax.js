/* ******************************************************************** 
 *  File: /hotaru_ajax.js
 *  Purpose: Calls functions behind the scenes for live updating without page refreshes.
 *  Notes: ---
 ********************************************************************** */

var xmlhttp
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
  try {
  xmlhttp=new ActiveXObject("Msxml2.XMLHTTP")
 } catch (e) {
  try {
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
  } catch (E) {
   xmlhttp=false
  }
 }
@else
 xmlhttp=false
@end @*/

if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
{
  try {
	xmlhttp = new XMLHttpRequest ();
  }
  catch (e) {
  xmlhttp = false}
}

function myXMLHttpRequest ()
{
  var xmlhttplocal;
  try {
  	xmlhttplocal = new ActiveXObject ("Msxml2.XMLHTTP")}
  catch (e) {
	try {
	xmlhttplocal = new ActiveXObject ("Microsoft.XMLHTTP")}
	catch (E) {
	  xmlhttplocal = false;
	}
  }

  if (!xmlhttplocal && typeof XMLHttpRequest != 'undefined') {
	try {
	  var xmlhttplocal = new XMLHttpRequest ();
	}
	catch (e) {
	  var xmlhttplocal = false;
	}
  }
  return (xmlhttplocal);
}

var ajax = Array ();
var returnvalue = Array ();

/* ******************************************************************** 
 *  Function: hide_show_replace
 *  Parameters: baseurl, type, id, url, parameters, var4, var5 (see function for details)
 *  Purpose: Used for calling a function to do something and showing the response, without reloading the page.
 *  Notes: ---
 ********************************************************************** */
	 
function hide_show_replace(baseurl, type, id, url, parameters, var4, var5)
{
	/* ******************************************************************** 	
		type = showhide, changetext
		id = container id to hide, show or replace
		url = where the file/function we need is
		parameters = parameter string, e.g. "action=do&id=4"
	 	var 4 and 5 are extras if necessary 
	******************************************************************** */
	
	if(type == "showhide") {
		var display_state=document.getElementById(id).style.display ? '' : 'none';
		document.getElementById(id).style.display = display_state;
	}

	if (xmlhttp) {
		target2 = document.getElementById (id);
		target2.innerHTML = "<img src='" + baseurl + "images/ajax-loader-mini.gif'>";	
		ajax[id] = new myXMLHttpRequest ();
		
		if (ajax) {
			ajax[id].open ("POST", url, true);
			ajax[id].setRequestHeader ('Content-Type',
					   'application/x-www-form-urlencoded');

			ajax[id].send (parameters);
			errormatch = new RegExp ("^ERROR:");
			target1 = document.getElementById (id);
			ajax[id].onreadystatechange = function () {
				if (ajax[id].readyState == 4) {
					returnvalue[id] = ajax[id].responseText;			
					if (returnvalue[id].match (errormatch)) {
						returnvalue[id] = returnvalue[id].substring (6, returnvalue[id].length);						
						target2 = document.getElementById (id);
						target2.innerHTML = returnvalue[id];						
					} else {
						target2 = document.getElementById (id);
						target2.innerHTML = returnvalue[id];
					}
				}
			}
		}
	}
}


/* ******************************************************************** 
 *  Function: widget_moved
 *  Parameters: baseurl, str (string of positions returned from EasyWidgets
 *  Purpose: Used specifically for moving plugins around in Plugin Management. 
 *           This function calls the admin_plugins file which updates thestatus of each plugin 
 *  Notes: The jQuery code and baseurl definition is in admin_themes' header.php
 ********************************************************************** */

function widget_moved(baseurl, str)
{
	url = baseurl+"admin/admin_plugins.php";
	if (xmlhttp) {
		target2 = document.getElementById ('ajax-loader');
		target2.innerHTML = "<img src='" + baseurl + "images/ajax-loader-mini.gif'>";	
		ajax['ajax-loader'] = new myXMLHttpRequest ();
		
		if (ajax) {
			ajax['ajax-loader'].open ("POST", url, true);
			ajax['ajax-loader'].setRequestHeader ('Content-Type',
					   'application/x-www-form-urlencoded');

			ajax['ajax-loader'].send ("position="+str);
			errormatch = new RegExp ("^ERROR:");
			ajax['ajax-loader'].onreadystatechange = function () {
				if (ajax['ajax-loader'].readyState == 4) {
					returnvalue['ajax-loader'] = ajax['ajax-loader'].responseText;			
					if (returnvalue['ajax-loader'].match (errormatch)) {
						returnvalue['ajax-loader'] = returnvalue['ajax-loader'].substring (6, returnvalue['ajax-loader'].length);						
						target2 = document.getElementById ('ajax-loader');
						target2.innerHTML = returnvalue['ajax-loader'];						
					} else {
						target2 = document.getElementById ('ajax-loader');
						target2.innerHTML = returnvalue['ajax-loader'];
					}
				}
			}
		}
	}
}


