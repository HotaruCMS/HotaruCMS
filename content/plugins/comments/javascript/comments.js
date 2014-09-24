/* **************************************************************************************************** 
 *  File: /plugins/comments/comments.js
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
	$(".comment_reply_link").click(function () {
		var target = $(this).parents(".comment");
		target = $(target).next("div").next("div");
		target.fadeToggle();
		
		target2 = $(target).find("div.comment_status"); // finds div in form
		target2.hide();

                return false;
        });  
        
	// Show/Hide box 
	$(".comment_edit_link").click(function () {
		var target = $(this).parents(".comment");
		target = $(target).next("div").next("div"); // finds div surrounding form
		target.fadeToggle();

		target2 = $(target).find("div.comment_status"); // finds div in form
		target2.show();

                return false;
        }); 
        
	// Show/Hide comment content
	$(".comment_show_hide").click(function () {
		var target = $(this).parents(".comment_header");
		target = $(target).next("div").next("div"); // finds div surrounding form
		target.fadeToggle();

		target2 = $(target).find("div.comment_status"); // finds div in form
		target2.show();

                return false;
        }); 
        
});  


/**
 * Reply Comment
 *
 * @param string baseurl
 * @param int comment_id
 * @param string comment_content
 * @param string submit button text
 */
function reply_comment(baseurl, comment_id, comment_submit)
{
	document.getElementById("comment_process_"+comment_id).value = 'newcomment';	
	document.getElementById("comment_content_"+comment_id).innerHTML = '';
	document.getElementById("comment_submit_"+comment_id).value = comment_submit;	
}

/**
 * Edit Comment
 *
 * @param string baseurl
 * @param int comment_id
 * @param string comment_content
 * @param string submit button text
 */
function edit_comment(baseurl, comment_id, comment_content, comment_submit)
{
	document.getElementById("comment_process_"+comment_id).value = 'editcomment';	
	document.getElementById("comment_content_"+comment_id).innerHTML = urldecode(comment_content);
	document.getElementById("comment_submit_"+comment_id).value = comment_submit;	
}
