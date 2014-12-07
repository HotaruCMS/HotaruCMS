<?php
namespace Hotaru\Models;

class BaseModel //extends \Illuminate\Database\Eloquent\Model
{
    public static function boot()
    {
        parent::boot();
//        static::creating(function($model)
//        {
//            $user = $h->currentUser;            
//            $model->created_by = $user->id;
//            $model->updated_by = $user->id;
//        });
//        static::updating(function($model)
//        {
//            $user = Auth::user();
//            $model->updated_by = $user->id;
//        });        
    }

}

