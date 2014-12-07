<?php

namespace Hotaru\Models;

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
    
    public static function getForUser($postId, $userId)
    {
        $model = self::where('vote_post_id', '=', $postId)
                ->where('vote_user_id', '=', $postId)
                ->where('vote_user_ip', '=', $userId)
                ->pluck('vote_rating', '-999');  // exclude flags
                
        return $model;
    }
    
    public static function getForAnonUser($postId, $ipAddress)
    {
        /*  include user_id = 0 since if registered user votes after anon at same ip, 
            we dont want to delete both votes later if anon user unvotes*/
        
        $model = self::where('vote_post_id', '=', $postId)
                ->where('vote_user_ip', '=', $ipAddress)
                ->where('vote_user_id', '=', 0)
                ->pluck('vote_rating', '-999');  // exclude flags
                
        return $model;
    }
    
   
}

                       
