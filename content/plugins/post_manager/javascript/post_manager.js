/* ****************************************************************************************************
 *  File: /plugins/post_manager/javascript/post_manager.js
 *  Purpose: Ajax for Post_Manager Plugin
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

    $("#post_man_table tr").hover(function(){       
        $this = $(this);       
        var td =$(this).children('.pm_author');        
        var icons = td.children('.user_manager_name_icons');        
        icons.toggle();        
    });

    $("#post_man_table .table_tr_details").hover(function() {        
        var trparent = $(this).parent('tbody');
        var position = $('tr', $(this).parent('tbody')).index(this);
        position -=1;       
        var tr=trparent.children('tr:eq('+ position +')');              
        var td =tr.children('.pm_author');
        var icons = td.children('.user_manager_name_icons');
        icons.toggle();
    });

});

