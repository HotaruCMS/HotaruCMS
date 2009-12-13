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
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
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

// Custom JQuery functions:

// FADE TOGGLE
jQuery.fn.fadeToggle = function(speed, easing, callback) {
   return this.animate({opacity: 'toggle'}, speed, easing, callback);

}; 

/* ************************************* */

// JQuery Function calls:

$(document).ready(function(){

	// Fade message
	$(".message").css({display: "none"}).fadeIn("slow");
	
        
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
		var target = $(this).next("form");
                target.fadeToggle();
                return false;
        });  
 
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