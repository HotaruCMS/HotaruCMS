<?php

namespace Hotaru\Models2;

class Vote extends BaseModel
{
    protected $table = 'postvotes';
    
    public function post()
    {
        return $this->hasOne('\Hotaru\Models\Post', 'post_id', 'postvote_post_id');
    }
    
    public function user()
    {
        return $this->hasOne('\Hotaru\Models\User', 'user_id', 'postvote_user_id');
    }
    
    
    // calls
    
    public static function getForUser($h, $postId, $userId)
    {
        $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating != %d LIMIT 1";
        $model = $h->db->get_var($h->db->prepare($sql, $postId, $userId, -999)); // exclude flags
        
        return $model;
    }
    
    public static function getForPost($h, $postId)
    {
        $sql = "SELECT post_votes_up, post_votes_down, post_status, post_date FROM " . TABLE_POSTS . " WHERE post_id = %d LIMIT 1";
        $model = $h->db->get_row($h->db->prepare($sql, $postId));
        
        return $model;
    }
    
    public static function getForAnonUser($h, $postId, $ipAddress)
    {
        /*  include user_id = 0 since if registered user votes after anon at same ip, 
            we dont want to delete both votes later if anon user unvotes*/
        $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_user_ip = %s AND vote_rating != %d LIMIT 1";
        $model = $h->db->get_var($h->db->prepare($sql, $postId, 0, $ipAddress, -999)); // exclude flags 
                
        return $model;
    }
    
   
}

                       