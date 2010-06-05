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

/* Hide/Show Alert choices below each story */

$(document).ready(function(){

	// Show/Hide box 
	$(".alert_link").click(function () {
		var target = $(this).parents('div').next('div').children('div.alert_choices');
                target.fadeToggle();
                return false;
        });


       if ($('#loggedIn').hasClass('loggedIn_true')) {
            $(".show_post_title a").click(function(event) {
                if (vote_on_url_click == "checked") {
                    if ($(this).hasClass('click_to_source')) {
                        event.preventDefault();
                        var post_id = $(this).parent().parent().attr("id");
                        var parts = post_id.split('_');
                        post_id = parts[parts.length-1];

                        vote( post_id, 10, 'link' );
                        link = $(this).attr('href');

                        setTimeout(function () {
                            window.location.href = link
                        }, 500);
                        return false;
                        }
                    }
                    return true;
            });
       }

}); 

/* ******************************************************************** 
 *  Function: vote
 *  Parameters: user ip, post id
 *  Purpose: Used for instant voting without reloading the page
 *  Notes: ---
 ********************************************************************** */
	 
function vote(id, rating, referer)
{
	sendurl = BASEURL +"content/plugins/vote/vote_functions.php";
	
	$target_votes = $("#votes_"+id);
	$target_text_vote = $("#text_vote_"+id);
	$target_text_unvote = $("#text_unvote_"+id);

    var formdata = "post_id="+id+"&rating="+rating+"&referer="+referer;

        $.ajax(
            {
            type: 'post',
            url: sendurl,
            data: formdata,
            beforeSend: function () {
                            $target_votes.addClass('vote_color_top_clicked');
                    },
            error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                             $target_votes.html('err');
            },
            success: function(data) { // success means it returned some form of json code to us. may be code with custom error msg
                    if (data.error === true || referer === "link") {
                    }
                    else {                        
                        $target_votes.html(data.votes);
                        $target_votes.addClass('vote_color_top_just_voted');
                        if(rating > 0) {
                            $target_text_vote.css('display','none');
                            $target_text_unvote.css('display','block');
                        } else if(rating < 0) {
                            $target_text_vote.css('display','block');
                            $target_text_unvote.css('display','none');
                        }
                    }
            },
            dataType: "json"
        });
	
}