/* **************************************************************************************************** 
 *  File: /plugins/widgets/javascript/widgets.js
 *  Purpose: Ajax for Widgets Plugin
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
			
    // start submit function //
    $(".widget").click(	function(){ $.fn.widget_onoff($(this)); });

});	


    $.fn.widget_onoff = function(widget) {
        // Get the current widget
        var currentId = widget.attr("id");
        var widget_image = widget.children("img").attr("src");
        this.widget_image = $(widget_image);
        var action = '';

        var image_names = widget_image.split('/');
        var image_name = image_names[image_names.length-1];
        if (image_name == "active.png") {  action = 'action=disable'; } else { action = 'action=enable'; }

        //alert(image_name);
        var formdata = 'plugin=widgets&' + action + '&widget=' + currentId;
        var sendurl = BASEURL + 'content/plugins/widgets/widgets_functions.php';

        $.ajax(
            {
            type: 'post',
            url: sendurl,
            data: formdata,
            beforeSend: function () {
                            widget.html('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>');
                    },
            error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                            widget.html('ERROR');
            },
            success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                    if (data.error === true) {
                    }
                    else
                    {
                        var img_src = "";
                        // get required image based on returned data showing new status
                        if(data.enabled == 'true') { img_src = "active.png"; } else { var img_src = "inactive.png"; }
                        widget.html('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/' + img_src + '"/>');
                    }
                    $('.message').html(data.message).addClass(data.color, 'visible');
            },
            dataType: "json"
        });
    }
