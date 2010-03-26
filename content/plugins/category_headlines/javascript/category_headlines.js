/* **************************************************************************************************** 
 *  File: /plugins/category_headlines/category_headlines.js
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
    $("#submit_button").click(function () {

        var campaign = $("form#category_headlines_settings_form").serialize();
        var formdata =  campaign + "&action=save";
        var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=category_headlines';

        $.ajax(
            {
            type: 'post',
            url: sendurl,
            data: formdata,
            beforeSend: function () { 
                            $('#error_message').html('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>');
                    },
            error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                             $('#error_message').html('There was an error with the data');
            },
            success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                    $error_message = "";
                    if (data.errors > 0) {
                         $error_mesage = 'There are a total of ' + data.errors + " problems to fix<br/>";
                         if (data.basic.length > 0) {$error_message += 'Basic: ' + data.basic + "<br/>";}
                    }
                    else
                    {
                        var img_src = "";
                        // get required image based on returned data showing new status
                        if(data.enabled == 'true') {img_src = "active.png";} else {img_src = "inactive.png";}
                        $('#edit_buttons').html('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/' + img_src + '"/>');
                        $error_message += "Your changes have been saved."
                    }
                    $('#error_message').html($error_message);
            },
            dataType: "json"
        });
        return false;
    });
});
             