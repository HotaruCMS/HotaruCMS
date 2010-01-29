/* **************************************************************************************************** 
 *  File: /content/admin_themes/admin_default/javascript/admin_default.js
 *  Purpose: JQuery for admin_default
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


jQuery('document').ready(function($) {

	$('.accordion li').has('ul').click(function() {
		
                if ($(this).children("ul").is(":hidden")) {

                    $(this).children("ul").slideDown("slow");
                } else {
                    $(this).children("ul").slideUp("slow");
                }

		
		
	});

    $('#admin_theme_theme_activate').click(function() {        
        var theme = $(this).attr("name");       
        var formdata = 'admin=theme_settings&theme='  + theme;
		var sendurl = BASEURL + "admin_index.php?page=settings";

        $.ajax(
			{
			type: 'post',
				url: sendurl,
				data: formdata,
				beforeSend: function () {
						$('#admin_theme_theme_activate').html('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>&nbsp;Attempting to activate theme.');
					},
				error: 	function(XMLHttpRequest, textStatus, errorThrown) {
						$(this).html('ERROR');
				},
				success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
					if (data.error === true) {
					}
					else
					{										
                        $('#admin_theme_theme_activate').html(data.message);
					}
					$('.message').html(data.message).addClass(data.color, 'visible');
				},
				dataType: "json"
		});

    });


	
});	