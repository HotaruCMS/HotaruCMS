/* ****************************************************************************************************
 *  File: /plugins/comment_manager/javascript/comment_manager.js
 *  Purpose: Ajax for Comment_Manager Plugin
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

    $("#table_list tr").hover(function(){
        //alert('hi');
        $this = $(this);
        var td =$(this).children('.cm_author');
        var icons = td.children('.user_manager_name_icons');
        icons.toggle();
    });

});

