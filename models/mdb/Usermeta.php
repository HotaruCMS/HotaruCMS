<?php

namespace Hotaru\Models2;

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
    
    
    public static function getProfileSetting($h, $userId, $type)
    {
        $query = "SELECT usermeta_value FROM " . DB_PREFIX . "usermeta WHERE usermeta_userid = %d AND usermeta_key = %s LIMIT 1";
        $sql = $h->db->prepare($query, $userId, $type);
        
        $h->smartCache('on', 'usermeta', 60, $sql); // start using database cache
        $model = $h->db->get_var($sql);        
        $h->smartCache('off'); // stop using database cache
              
        return $model;
    }
    
    
    public static function getUserFlags($h)
    {
        $query = "SELECT * FROM " . DB_PREFIX . "usermeta WHERE usermeta_key = %s";
        $sql = $h->db->prepare($query, 'stop_spam_flags');
        
        $model = $h->db->get_results($sql);     
        
        return $model;
    }
}
