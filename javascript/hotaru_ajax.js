/* **************************************************************************************************** 
 *  File: /javascript/hotaru_ajax.js
 *  Purpose: Calls functions behind the scenes for live updating without page refreshes.
 *  Notes: ---
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 *   Note: Part of the source code in this file is from open source projects, copyright as follows:
 *
 *   Portions are Copyright (C) 2005 by Ricardo Galli <gallir at uib dot es>.
 *   Portions are Copyright (C) 2005 - 2008 by Pligg <www.pligg.com>.
 *   Portions are Copyright (C) 2008 - 2009 by the Social Web CMS Team <swcms@socialwebcms.com>.
 *
 **************************************************************************************************** */

var xmlhttp=false;
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
 *  Parameters: baseurl, type, id, url, parameters (see function for details)
 *  Purpose: Used for calling a function to do something and showing the response, without reloading the page.
 *  Notes: ---
 ********************************************************************** */
	 
function hide_show_replace(baseurl, type, id, url, parameters)
{
	/* ******************************************************************** 	
		type = showhide, changetext
		id = container id to hide, show or replace
		url = where the file/function we need is
		parameters = parameter string, e.g. "action=do&id=4"
	******************************************************************** */
	
	if(type == "showhide") {
		var display_state=document.getElementById(id).style.display ? '' : 'none';
		document.getElementById(id).style.display = display_state;
	}

	if (xmlhttp) {
		target1 = document.getElementById (id);
		target1.innerHTML = "<img src='" + baseurl + "admin/themes/admin_default/images/ajax-loader-mini.gif'>";	
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
						target1 = document.getElementById (id);
						target1.innerHTML = returnvalue[id];						
					} else {
						target1 = document.getElementById (id);
						target1.innerHTML = returnvalue[id];
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
		target1 = document.getElementById ('ajax-loader');
		target1.innerHTML = "<img src='" + baseurl + "admin/themes/admin_default/images/ajax-loader-mini.gif'>";	
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
						target1 = document.getElementById ('ajax-loader');
						target1.innerHTML = returnvalue['ajax-loader'];						
					} else {
						target1 = document.getElementById ('ajax-loader');
						target1.innerHTML = returnvalue['ajax-loader'];
					}
				}
			}
		}
	}
}



/* ******************************************************************** 
 *  Function: submit_url
 *  Parameters: baseurl, id (block to show/hide), url (fetch_source.php)
 *  Purpose: Used for calling a file to fetch remote content then displaying it.
 *  Notes: ---
 ********************************************************************** */
	 
function submit_url(baseurl, url, parameters)
{
	var source_url = document.getElementById('source_url').value;
	
	if (xmlhttp) {
		mycontent = "source_url="+escape(source_url);
		target2 = document.getElementById ('ajax_loader');
		target2.innerHTML = "<img src='" + baseurl + "admin/themes/admin_default/images/ajax-loader-mini.gif'>";	
		ajax['submit_return_value'] = new myXMLHttpRequest ();
		
		if (ajax) {
			ajax['submit_return_value'].open ("POST", url, true);
			ajax['submit_return_value'].setRequestHeader ('Content-Type',
					   'application/x-www-form-urlencoded');

			ajax['submit_return_value'].send (mycontent);
			errormatch = new RegExp ("^ERROR:");
			target1 = document.getElementById ('submit_return_value');
			ajax['submit_return_value'].onreadystatechange = function () {
				if (ajax['submit_return_value'].readyState == 4) {
					returnvalue['submit_return_value'] = ajax['submit_return_value'].responseText;
					if (returnvalue['submit_return_value'].match (errormatch)) {
						returnvalue['submit_return_value'] = returnvalue['submit_return_value'].substring (6, returnvalue['submit_return_value'].length);						
						target1 = document.getElementById ('submit_return_value');
						target1.value = returnvalue['submit_return_value'];						
					} else {
						target1 = document.getElementById ('submit_return_value');
						target1.value = returnvalue['submit_return_value'];
						target2.innerHTML = "&nbsp;";
					}
				}
			}
		}
	}
}
