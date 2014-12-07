<?php

namespace Hotaru\Models;

class Usermeta extends BaseModel
{
    //Here active use - BaseModel
    public static function boot()
    {
        parent::boot();
    }
    
    protected $table = 'usermeta';
    
    protected $primaryKey = 'usermeta_id';
    
    const CREATED_AT = 'usermeta_updatedts';
    
    
    public static function getProfileSetting($userId, $type)
    {
        $model = self::where('usermeta_userid', $userId)->where('usermeta_key', $type)->pluck('usermeta_value');
        return $model;
    }
}
