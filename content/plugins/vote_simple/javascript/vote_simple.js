/* **************************************************************************************************** 
 *  File: /javascript/vote_simple.js
 *  Purpose: Fetches the title of the url being submitted
 *  Notes: This file is part of the Vote plugin.
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

/* Hide/Show Alert choices below each story */

$(document).ready(function(){

	// Show/Hide box 
	$(".alert_link").click(function () {
		var target = $(this).parents('div').next('div').children('div.alert_choices');
                target.fadeToggle();
                return false;
        });  
        
}); 

/* ******************************************************************** 
 *  Function: vote
 *  Parameters: user ip, post id
 *  Purpose: Used for instant voting without reloading the page
 *  Notes: ---
 ********************************************************************** */
	 
function vote(baseurl, ip, id, rating)
{
	url = baseurl+"content/plugins/vote_simple/vote_simple_functions.php";
	
	var target_votes = document.getElementById("votes_"+id);
	var target_text_vote = document.getElementById("text_vote_"+id);
	var target_text_unvote = document.getElementById("text_unvote_"+id);
	
	if (xmlhttp) {
		mycontent = "baseurl="+baseurl+"&user_ip="+ip+"&post_id="+id+"&rating="+rating;
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
							

					target_votes.innerHTML = returnvalue.votes;
					if(rating == 'positive') {
						target_text_vote.style.display = 'none';
						target_text_unvote.style.display = '';
					} else {
						target_text_vote.style.display = '';
						target_text_unvote.style.display = 'none';
					}

				} 
			}
		}
	}
}