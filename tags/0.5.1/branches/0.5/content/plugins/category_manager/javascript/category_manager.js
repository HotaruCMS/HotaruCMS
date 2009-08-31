/* **************************************************************************************************** 
 *  File: /plugins/category_manager/cat_man_jquery.js
 *  Purpose: Drop-down boxes for managing categories
 *  Notes: Uses Hotaru's built-in fadeToggle function.
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

// Function calls:

$(document).ready(function(){

	// Show/Hide box 
	$(".cat_man_drop_down").click(function () {
		var target = $(this).next().next();
                target.fadeToggle();
                return false;
        });  
        
});  