/* **************************************************************************************************** 
 *  File: /javascript/jQuery/hotaru_jquery.js
 *  Purpose: Provides common cosmetic functions, e.g. drop-down boxes
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

// Custom functions:

// FADE TOGGLE
jQuery.fn.fadeToggle = function(speed, easing, callback) {
   return this.animate({opacity: 'toggle'}, speed, easing, callback);

}; 

/* ************************************* */

// Function calls:

$(document).ready(function(){

	// Fade message
	$(".message").css({display: "none"}).fadeIn("slow");
	
        
	// Show/Hide plugin details
	$(".plugin_drop_down").click(function () {
		var target = $(this).parents("tr").next("tr");
                target.fadeToggle();
                return false;
        });   
        
	// Hide plugin details
	$(".plugin_hide_details").click(function () {
                $(this).parents("tr").fadeOut();
                return false;
        });  
        
	// Show/Hide forgoot password form
	$(".forgot_password").click(function () {
		var target = $(this).next("form");
                target.fadeToggle();
                return false;
        });  
 
});