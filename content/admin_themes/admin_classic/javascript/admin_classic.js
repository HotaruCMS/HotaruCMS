/* **************************************************************************************************** 
 *  File: /content/admin_themes/admin_classic/javascript/admin_classic.js
 *  Purpose: JQuery for admin_classic
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
 *   Copyright (c) 2010 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */


jQuery('document').ready(function($) {

    $('.accordion li').has('ul').addClass('arrow');

	$('li.arrow').click(function(e) {
        var clicked = jQuery(e.target);

        if(!clicked.is('li.arrow')) {
            return;
        }

        e.preventDefault();

        if ($(this).children("ul").is(":hidden")) {
            $(this).children("ul").slideDown("slow");
        } else {
            $(this).children("ul").slideUp("slow");
        }		
	});       

    $('.warning_slash').blur(function() {
        var value = $(this).val();
        var length = value.length;
        var check = value.substring(length-1,length);        
        var notes = $(this).parent().parent().children('td:eq(3)'); 
        if (check != '/' ) { notes.addClass('red'); } else { notes.removeClass('red');}
    });


    $('#admin_theme_theme_activate').click(function() {        
        var theme = $(this).attr("name");       
        var formdata = 'admin=theme_settings&theme='  + theme;
		var sendurl = SITEURL + "admin_index.php?page=settings";

        $.ajax(
			{
			type: 'post',
				url: sendurl,
				data: formdata,
				beforeSend: function () {
						$('#admin_theme_theme_activate').html('<img src="' + SITEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>&nbsp;Attempting to activate theme.');
					},
				error: 	function(XMLHttpRequest, textStatus, errorThrown) {
						$('#admin_theme_theme_activate').html('ERROR');
                        $('#admin_theme_theme_activate').removeClass('power_on').addClass('warning_on');
				},
				success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
					if (data.error === true) {
                        $('#admin_theme_theme_activate').removeClass('power_on').addClass('warning_on');
					}
					else
					{                        
                        $('#admin_theme_theme_activate').html(data.message);
                        $('#admin_theme_theme_activate').removeClass('power_on').addClass('tick_on');
					}
					$('.message').html(data.message).addClass(data.color, 'visible');
				},
				dataType: "json"
		});

    });


	
});	
