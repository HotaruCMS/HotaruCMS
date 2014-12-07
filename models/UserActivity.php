<?php

namespace Hotaru\Models;

class UserActivity extends BaseModel
{
    protected $table = 'useractivity';
    
    public function user()
    {
        return $this->hasOne('\Hotaru\Models\User', 'user_id', 'useract_userid');
    }
    
//    public function post()
//    {
//        return $this->hasMany('\Hotaru\Models\Post', 'post_id', 'useract_id_post_id');
//    }
}
