<?php
/* ********** PLUGIN *********************************************************************************
 * file: plugins/vote/functions.php
 * purpose: Voting functions that are performed behind the scenes with Ajax
 *
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

include_once('../../../hotaru_header.php');	// Not the cleanest way of getting to the root...

global $cage, $db, $current_user, $post, $lang, $plugin;

$plugin->include_language_file('vote');

if($cage->post->keyExists('post_id')) {
	$post_id = $cage->post->testInt('post_id');
	$vote_type = $cage->post->testAlnumLines('type');
	$vote_rating = $cage->post->testAlnumLines('rating');
	if($current_user->logged_in) {
		
		if($vote_rating == 'positive') {
			$sql = "UPDATE " . table_posts . " SET post_votes_up=post_votes_up+1 WHERE post_id = %d";
			$db->query($db->prepare($sql, $post_id));
			$sql = "INSERT INTO " . table_votes . " (vote_post_id, vote_user_id, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, CURRENT_TIMESTAMP, %s, %s, %d)";
			$db->query($db->prepare($sql, $post_id, $current_user->id, $vote_type, $vote_rating, $current_user->id));
		} else {
			$sql = "UPDATE " . table_posts . " SET post_votes_down=post_votes_down+1 WHERE post_id = %d";
			$db->query($db->prepare($sql, $post_id));
			$sql = "INSERT INTO " . table_votes . " (vote_post_id, vote_user_id, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, CURRENT_TIMESTAMP, %s, %s, %d)";
			$db->query($db->prepare($sql, $post_id, $current_user->id, $vote_type, $vote_rating, $current_user->id));
		}
		
		$sql = "SELECT post_votes_up, post_votes_down FROM " . table_posts . " WHERE post_id = %d";
		$votes = $db->get_row($db->prepare($sql, $post_id));
		
		if($vote_type == 'vote_bury') {
			$json_array = array('votes'=>$votes->post_votes_up, 'text'=>$lang["vote_button_voted"]);
			echo json_encode($json_array);
		} elseif($vote_type == 'up_down') {
			$json_array = array('votes'=>($votes->post_votes_up - $votes->post_votes_down), 'text'=>$lang["vote_button_voted"]);
			echo json_encode($json_array);
		} elseif($vote_type == 'yes_no' && $vote_rating == 'positive') {
			$json_array = array('votes'=>$votes->post_votes_up, 'text'=>$lang["vote_button_voted"]);
			echo json_encode($json_array);
		} elseif($vote_type == 'yes_no' && $vote_rating == 'negative') {
			$json_array = array('votes'=>$votes->post_votes_down, 'text'=>$lang["vote_button_voted"]);
			echo json_encode($json_array);
		}
	}
}

?>