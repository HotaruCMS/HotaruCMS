/* **************************************************************************************************** 
 *  File: /javascript/submit.js
 *  Purpose: Fetches the title of the url being submitted
 *  Notes: This file is part of the Submit plugin.
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
 *  Function: submit_url
 *  Parameters: baseurl, id (block to show/hide), url (fetch_source.php)
 *  Purpose: Used for calling a file to fetch remote content then displaying it.
 *  Notes: ---
 ********************************************************************** */
	 
function submit_url(baseurl, url, parameters)
{
	var post_orig_url = document.getElementById('post_orig_url').value;
	
	if (xmlhttp) {
		mycontent = "post_orig_url="+escape(post_orig_url);
		loader1 = document.getElementById ('ajax_loader');
		loader1.innerHTML = "<img src='" + baseurl + "admin/themes/admin_default/images/ajax-loader-mini.gif'>";	
		ajax['response'] = new myXMLHttpRequest ();
		
		if (ajax) {
			ajax['response'].open ("POST", url, true);
			ajax['response'].setRequestHeader ('Content-Type',
					   'application/x-www-form-urlencoded');

			ajax['response'].send (mycontent);
			ajax['response'].onreadystatechange = function () {
				if (ajax['response'].readyState == 4) {
					try{
						returnvalue = ajax['response'].responseText;
					}
					catch(e) {
						alert("Unable to fetch the title from this url. Sorry!");
					}
					target_title = document.getElementById ('post_title');
					target_title.value = returnvalue;
					loader1.innerHTML = "&nbsp;";
				} 
			}
		}
	}
}