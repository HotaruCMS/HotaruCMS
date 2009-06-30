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

	// SlideToggle example
	$(".widget-header a").click(function () {
		var parentTag = $(this).parent("div").next();
                parentTag.slideToggle();
                return false;
	});

	// Show/Hide box
	$(".dropdown").click(function () {
		var parentTag = $(this).parents("ul").children("ul#plugin_settings_list");
                parentTag.fadeToggle();
                return false;
        });  
        
	// Fade message
	$(".message").css({display: "none"}).fadeIn("slow");
 
});