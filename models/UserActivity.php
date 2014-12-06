<?php

namespace HotaruModels;

class UserActivity extends BaseModel
{
    protected $table = 'useractivity';
    
    public function user()
    {
        return $this->hasOne('\HotaruModels\User', 'user_id', 'useract_userid');
    }
    
//    public function post()
//    {
//        return $this->hasMany('\HotaruModels\Post', 'post_id', 'useract_id_post_id');
//    }
}
