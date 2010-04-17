<?php

class FollowFuncs
{     

    /**
     * Get Follow Count
     */
     public function getFollowCount($h, $type = 'follower', $user_id = 0)
     {
	$user_id == 0 ? $user_id = $h->vars['user']->id : '';
        if ($type == 'follower') { $type2 = "follower_user_id"; $type1 = "following_user_id"; } else { $type2 = "following_user_id"; $type1 = "follower_user_id"; }
        
        $sql = "SELECT count(*) AS number FROM " . DB_PREFIX . "follow WHERE " . $type1 . " = %s";
        $count = $h->db->get_var($h->db->prepare($sql, $user_id));
        
        return $count;
     }

     /**
     * Get Follow Users
     */
     public function getFollowUsers($h, $type = 'follower', $user_id = 0)
     {
	$user_id == 0 ? $user_id = $h->vars['user']->id : '';
        if ($type == 'follower') { $type1 = "follower_user_id"; $type2 = "following_user_id"; } else { $type1 = "following_user_id"; $type2 = "follower_user_id"; }

	$sql = "SELECT user_id, user_username FROM " . DB_PREFIX . "users AS USERS JOIN " . DB_PREFIX . "follow AS FOLLOW on FOLLOW." . $type1 . " = USERS.user_id WHERE FOLLOW." . $type2 . " = %s";
	$query = $h->db->prepare($sql, $user_id);

        return $query;
     }
     
     
    /**
     * Check Follow status of User by Current User
     */
     public function checkFollow($h, $type = 'follower', $user_id = 0)
     {
	$user_id == 0 ? $user_id = $h->vars['user']->id : '';
	if ($type == 'following') { $type1 = "follower_user_id"; $type2 = "following_user_id"; } else { $type1 = "following_user_id"; $type2 = "follower_user_id"; }

        $sql = "SELECT count(*) FROM " . DB_PREFIX . "follow WHERE " . $type1 ." = %d AND " . $type2 . " = %d";
  	$result = $h->db->get_var($h->db->prepare($sql, $h->currentUser->id, $user_id));

        return $result;
     }
     
     
    /**
     * Update Follow Status
     */
     public function updateFollow($h, $status = "follow", $user_id = 0)
     {
	$user_id == 0 ? $user_id = $h->vars['user']->id : '';
	$currentFollow = $this->checkFollow($h, 'following', $user_id );	
	if ($status == "unfollow") {
	    if (!$currentFollow == 0) {
		// Delete record of following for this user
		$sql = "DELETE FROM " . DB_PREFIX . "follow WHERE (follower_user_id = %d AND following_user_id = %d)";
		$h->db->query($h->db->prepare($sql, $h->currentUser->id, $user_id));		
		return json_encode(array('result'=>'Follow'));  // Send back opposite so we can use words for buttons
	    }
	} else {
	    if ($currentFollow==0 ) {
		// Insert record of following for this user
		$sql = "INSERT INTO " . DB_PREFIX . "follow (follower_user_id, following_user_id) VALUES (%d, %d)";
		$h->db->query($h->db->prepare($sql, $h->currentUser->id, $user_id));
		return json_encode(array('result'=>'Unfollow'));  // Send back opposite so we can use words for buttons
	    }
	}
     }
        
}
?>