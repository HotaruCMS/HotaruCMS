<?php

namespace HotaruModels;

class Postvote extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'postvotes';
    
    public function postvote()
    {
        return $this->hasOne('\HotaruModels\User', 'user_id', 'vote_user_id');
    }
    
    public function post()
    {
        return $this->hasOne('\HotaruModels\Post', 'vote_post_id', 'post_id');
    }
}
