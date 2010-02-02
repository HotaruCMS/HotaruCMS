/* **************************************************************************************************** 
 *  File: /plugins/jquery_test/jquery_test.js
 *  Purpose: JQuery Settings
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

jQuery('document').ready(function(){

	$(".run, #jquery-test-box").click(function(){

	  $("#jquery-test-box").animate({opacity: "0.1", left: "+=400"}, 1200)
	  .animate({opacity: "0.4", top: "+=160", height: "20", width: "20"}, "slow")
	  .animate({opacity: "1", left: "0", height: "100", width: "100", backgroundColor: "green"}, "slow")
	  .animate({top: "0"}, "fast")
	  .slideUp()
	  .slideDown("slow");	  

	  $("#jquery-test-return").html("The BASEURL for this Site is " + BASEURL);
	  return false;
	});

	$(".run-footer, #jquery-test-footer-box").click(function(){

	  $("#jquery-test-footer-box").animate( { backgroundColor: 'red' }, 1000)
			.animate( { backgroundColor: 'blue' }, 1000);
	  

	  $("#jquery-test-return").html("The BASEURL for this Site is " + BASEURL);
	  return false;
	});

});

