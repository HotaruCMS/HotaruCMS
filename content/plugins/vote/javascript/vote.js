/* **************************************************************************************************** 
 *  File: /javascript/vote.js
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
/* ******************************************************************** 
 *  Function: vote
 *  Parameters: user id, post id, vote type
 *  Purpose: Used for instant voting without reloading the page
 *  Notes: ---
 ********************************************************************** */
	 
function vote(baseurl, user, id, type, rating)
{
	url = baseurl+"content/plugins/vote/vote_functions.php";
	var target_votes = document.getElementById("vote_bury_votes_"+id);
	var target_text = document.getElementById("vote_bury_text_"+id);
	
	if (xmlhttp) {
		mycontent = "baseurl="+baseurl+"&user="+user+"&post_id="+id+"&type="+type+"&rating="+rating;
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
					
					target_votes.innerHTML = returnvalue.votes;
					target_text.innerHTML = returnvalue.text;
				} 
			}
		}
	}
}