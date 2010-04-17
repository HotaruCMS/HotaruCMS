/* **************************************************************************************************** 
 *  File: /plugins/journal/journal.js
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
	$(".show_post_edit").click(function () {
		var target = $(this).parents(".show_post_author_date");
		target = $(target).nextAll(".post_form"); // finds div surrounding form
		target.fadeToggle();
		return false;
	});
	
	// Show reply box
	$(".comment_form_fake").click(function () {
		$(this).hide();
		
		var target = $(this).next(); // next should be a form
		target.show(); // show the form
		$(".comment_textarea").val('');
		
		return false;
	});
	
	// hide reply box
	$(".comment_form form").focusout(function () {
		if ($(this).children('textarea').val() == '') {
			$(this).hide();
			
			$(".comment_form_fake").show(); // show the mini textarea
		}

		return false;
	});
	
	// Show/Hide box 
	$(".comment_edit_link").click(function () {
		var target = $(this).parents(".comment");
		var content = target.next(".comment_form").children("form");
		content.toggle();
		return false;
	});
	
});


/**
 * Reply to Post
 *
 * @param int post_id
 * @param string post_content
 * @param string submit button text
 */
function reply_post(post_id, post_submit)
{
	document.getElementById("post_process_"+post_id).value = 'newpost';	
	document.getElementById("post_content_"+post_id).innerHTML = '';
	document.getElementById("post_submit_"+post_id).value = post_submit;	
}

/**
 * Edit Post
 *
 * @param int post_id
 * @param string post_content
 * @param string submit button text
 */
function edit_post(post_id, post_content, post_submit)
{
	document.getElementById("post_process_"+post_id).value = 'editpost';	
	document.getElementById("post_content_"+post_id).innerHTML = urldecode(post_content);
	document.getElementById("post_submit_"+post_id).value = post_submit;	
}

/**
 * Edit Post
 *
 * @param int comment_id
 * @param string comment_content
 * @param string submit button text
 */
function edit_reply(comment_id, comment_content, comment_submit)
{
	document.getElementById("comment_process_"+comment_id).value = 'editcomment';	
	document.getElementById("comment_content_"+comment_id).innerHTML = urldecode(comment_content);
	document.getElementById("comment_submit_"+comment_id).value = comment_submit;	
}
