<?php

namespace Hotaru\Models;

class Postvote extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'postvotes';
    
    public function postvote()
    {
        return $this->hasOne('\Hotaru\Models\User', 'user_id', 'vote_user_id');
    }
    
    public function post()
    {
        return $this->hasOne('\Hotaru\Models\Post', 'vote_post_id', 'post_id');
    }
}
