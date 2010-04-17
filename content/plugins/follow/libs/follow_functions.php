<?php

class FollowFuncs
{

	/**
	 * Get Follow Count
	 */
	 public function getFollowCount($h, $type = 'follower', $user_id = 0)
	 {
		$user_id == 0 ? $user_id = $h->vars['user']->id : 0;
		
		if ($type == 'follower') {
			return $h->countFollowers($user_id);
		} else {
			return $h->countFollowing($user_id);
		}
	}


	 /**
	 * Get Follow Users
	 */
	 public function getFollowUsers($h, $type = 'follower', $user_id = 0)
	 {
		$user_id == 0 ? $user_id = $h->vars['user']->id : 0;
		
		if ($type == 'follower') {
			return $h->getFollowers($user_id, 'query');
		} else { 
			return $h->getFollowing($user_id, 'query');
		}
     }
     
     
    /**
     * Check Follow status of User by Current User
     */
     public function checkFollow($h, $type = 'follower', $user_id = 0)
     {
		$user_id == 0 ? $user_id = $h->vars['user']->id : 0;
		
		if ($type == 'following') {
			return $h->isFollowing($user_id);
		} else { 
			return $h->isFollower($user_id);
		}
     }
     
     
    /**
     * Update Follow Status
     */
     public function updateFollow($h, $status = "follow", $user_id = 0)
     {
		$user_id == 0 ? $user_id = $h->vars['user']->id : '';
			
		if ($status == "unfollow") {
			if ($h->isFollowing($user_id)) {
				$h->unfollow($user_id);
				return json_encode(array('result'=>'Follow'));  // Send back opposite so we can use words for buttons
			}			
		} else {
			if (!$h->isFollowing($user_id)) {
				$h->follow($user_id);
				return json_encode(array('result'=>'Unfollow'));  // Send back opposite so we can use words for buttons
			}			
		}

		// Make a final check of the updated status, so we can return the correct label, otherwise an error may be returned
		if ($h->isFollowing($user_id)) { return json_encode(array('result'=>'Unfollow')); } else { return json_encode(array('result'=>'Follow')); }
	}

}
?>
