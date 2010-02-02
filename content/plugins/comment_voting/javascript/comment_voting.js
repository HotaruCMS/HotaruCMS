/* **************************************************************************************************** 
 *  File: /javascript/comment_voting.js
 *  Purpose: Fetches the title of the url being submitted
 *  Notes: This file is part of the Comment Voting plugin.
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
 *   Note: Part of the source code in this file is from open source projects, copyright as follows:
 *
 *   Portions are Copyright (C) 2005 by Ricardo Galli <gallir at uib dot es>.
 *   Portions are Copyright (C) 2005 - 2008 by Pligg <www.pligg.com>.
 *   Portions are Copyright (C) 2008 - 2009 by the Social Web CMS Team <swcms@socialwebcms.com>.
 *
 **************************************************************************************************** */


/* ******************************************************************** 
 *  Function: comment_voting
 *  Parameters: baseurl, user ip, post id, comment id, rating
 *  Purpose: Used for instant voting without reloading the page
 *  Notes: ---
 ********************************************************************** */
	 
function comment_voting(baseurl, ip, postid, commentid, rating)
{
	url = baseurl+"content/plugins/comment_voting/comment_voting_functions.php";
	
	var target_comment_votes_up = document.getElementById("comment_votes_up_"+commentid);
	var target_comment_votes_up_link = document.getElementById("comment_votes_up_link_"+commentid);
	var target_comment_votes_up_text = document.getElementById("comment_votes_up_text_"+commentid);
	var target_comment_votes_down = document.getElementById("comment_votes_down_"+commentid);
	var target_comment_votes_down_link = document.getElementById("comment_votes_down_link_"+commentid);
	var target_comment_votes_down_text = document.getElementById("comment_votes_down_text_"+commentid);
	
	if (xmlhttp) {
		mycontent = "baseurl="+baseurl+"&user_ip="+ip+"&post_id="+postid+"&comment_id="+commentid+"&rating="+rating;
		ajax['response'] = new myXMLHttpRequest ();
		
		if (ajax) {
			ajax['response'].open ("POST", url, true);
			ajax['response'].setRequestHeader ('Content-Type',
					   'application/x-www-form-urlencoded');

			ajax['response'].send (mycontent);
			ajax['response'].onreadystatechange = function () {
				if (ajax['response'].readyState == 4) {
					try{
						var returnvalue = [];
						returnvalue = json_decode(ajax['response'].responseText);
					}
					catch(e) {
						alert("Unable to add your vote. Sorry!");
					}
						
					if(returnvalue.result) {
						alert(returnvalue.result);
						return;
					}
							
					if(rating > 0) {
						target_comment_votes_up.innerHTML = returnvalue.comments_up;
					} else {
						target_comment_votes_down.innerHTML = returnvalue.comments_down;
					}
					
					target_comment_votes_up_link.style.display = 'none';
					target_comment_votes_up_text.style.display = '';
					target_comment_votes_down_link.style.display = 'none';
					target_comment_votes_down_text.style.display = '';
				} 
			}
		}
	}
}