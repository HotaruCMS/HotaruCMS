/* ******* CUSTOM jQUERY INCLUDE FILE *********
File: jquery.hotaru.js
Purpose: Provides common functions, e.g. drop-down boxes
Notes: ---
***************************** */

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
});