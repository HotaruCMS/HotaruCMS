/* ****************************************************************************************************
 *  File: /plugins/autoreader/autoreader.js
 *  Purpose: Drop-down boxes for managing categories
 *  Notes:
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

jQuery('document').ready(function($) {

    var config = {
         sensitivity: 3, // number = sensitivity threshold (must be 1 or higher)
         interval: 200,  // number = milliseconds for onMouseOver polling interval
         over: doOpen,   // function = onMouseOver callback (REQUIRED)
         timeout: 200,   // number = milliseconds delay before onMouseOut
         out: doClose    // function = onMouseOut callback (REQUIRED)
    };

    function doOpen() {
        $(this).addClass("hover");
        $('ul:first',this).css('visibility', 'visible');
    }

    function doClose() {
        $(this).removeClass("hover");
        $('ul:first',this).css('visibility', 'hidden');
    }

    //$("ul.dropdown li").hoverIntent(config);

    //$("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");

    $.ajaxSetup({
       cache: false
	});

    $("ul.dropdown li a").click(function(event){
        event.preventDefault();

        var link = $(this).attr('name');
        var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=' + link;       

        $("#admin_plugin_content")
                .fadeOut("fast")
                .text("... loading ...")
                .load(sendurl)
                .fadeIn("fast");

                //When page loads...
                 
        return false;
    });
   
});


