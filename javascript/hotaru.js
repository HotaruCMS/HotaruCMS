/* **************************************************************************************************** 
 *  File: /javascript/hotaru.js
 *  Purpose: A mixed bag of Ajax, JQuery and other JavaScript functions
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
 *   Copyright (C) 2010 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

var xmlhttp=false;

if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
{
	try {
		xmlhttp = new XMLHttpRequest ();
	}
	catch (e) {
		xmlhttp = false
	}
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

// Custom JQuery functions:

// FADE TOGGLE
// used by category_manager, comments, vote plugin
jQuery.fn.fadeToggle = function(speed, easing, callback) {
	return this.animate({opacity: 'toggle'}, speed, easing, callback);

}; 

/* ************************************* */

// JQuery Function calls:

$(document).ready(function(){

	// Show/Hide table details (Plugin Management page and similar tables)
	$(".table_drop_down").click(function () {
		var target = $(this).parents("tr").next("tr");
		target.fadeToggle();
		return false;
	});

	// Hide table details (Plugin Management page and similar tables)
	$(".table_hide_details").click(function () {
		$(this).parents("tr.table_tr_details").fadeOut();
		return false;
	});  

	// Show/Hide forgot password form
	$(".forgot_password").click(function () {
		var target = $("#forgot_password_form");
		target.fadeToggle();
		return false;
	});  
	
	// Show/Hide generic
	$(".show_hide").click(function () {
		var target = $(".show_hide_target");
		target.fadeToggle();
		return false;
	});
 
});

/***********************************************
* encode and decode function for jQuery
* http://jqueryjournal.com/jquerys-url-encode-decode/
***********************************************/

$.extend({URLEncode:function(c){var o='';var x=0;c=c.toString();var r=/(^[a-zA-Z0-9_.]*)/;
  while(x<c.length){var m=r.exec(c.substr(x));
    if(m!=null && m.length>1 && m[1]!=''){o+=m[1];x+=m[1].length;
    }else{if(c[x]==' ')o+='+';else{var d=c.charCodeAt(x);var h=d.toString(16);
    o+='%'+(h.length<2?'0':'')+h.toUpperCase();}x++;}}return o;},
URLDecode:function(s){var o=s;var binVal,t;var r=/(%[^%]{2})/;
  while((m=r.exec(o))!=null && m.length>1 && m[1]!=''){b=parseInt(m[1].substr(1),16);
  t=String.fromCharCode(b);o=o.replace(m[1],t);}return o;}
});


/***********************************************
* Disable "Enter" key in Form script- By Nurul Fadilah(nurul@REMOVETHISvolmedia.com)
* This notice must stay intact for use
* Visit http://www.dynamicdrive.com/ for full source code
* Usage: <input type="text" onkeypress="return handleEnter(this, event)" id="" name="" value="" />
***********************************************/

function handleEnter (field, event) {
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13) {
		/* The following lines move the cursor to the next form field which works but we don't need it and it throws 2 Firebug errors.
		var i;
		for (i = 0; i < field.form.elements.length; i++)
			if (field == field.form.elements[i])
				break;
		i = (i + 1) % field.form.elements.length;
		field.form.elements[i].focus();
		*/
		return false;
	} 
	else
	return true;
}



