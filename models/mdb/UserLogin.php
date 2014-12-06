<?php

namespace HotaruModels2;

class UserLogin extends BaseModel
{
    protected $table = 'userlogin';
    
    public static function getLogin($h, $userId, $key)
    {
        $query = "SELECT * FROM " . TABLE_USERLOGINS . " WHERE user_id = %d AND provider_key = %s";
        $sql = $h->db->prepare($query, $userId, $key); 
        $model = $h->db->get_row($sql);
        
        //$model = self::where('user_id', $userId)->first(array('user_id', 'user_username', 'user_password', 'user_role', 'user_email', 'user_email_valid'));
        return $model;
    }
    
    public static function getLogins($h, $userId)
    {
        $query = "SELECT * FROM " . TABLE_USERLOGINS . " WHERE user_id = %d";
        $sql = $h->db->prepare($query, $userId); 
        $model = $h->db->get_results($sql);
        
        return $model;
    }

    
    // save
  
    public static function addLogin($h, $userId, $key, $provider = "COOKIE")
    {
        $sql = "INSERT INTO " . TABLE_USERLOGINS . " (user_id, login_provider, provider_key)"
                . " VALUES (%d, %s, %s)";

        $h->db->query($h->db->prepare($sql, $userId, $provider, $key));
    }

//    public static function updateClaim($h, $folder, $newStatus, $userId)
//    {
//        $sql = "UPDATE " . TABLE_USERCLAIMS . " SET plugin_enabled = %s, plugin_updateby = %d"
//                . " WHERE (plugin_folder = %s)";
//
//        $h->db->query($h->db->prepare($sql, $newStatus, $userId, $folder));
//    }
    
    
    // delete
    
    public static function removeLogin($h, $userId, $key)
    {
        $sql = "DELETE FROM " . TABLE_USERLOGINS
                . " WHERE (user_id = %d AND provider_key = %s) OR created_at < (NOW() - INTERVAL 30 DAY)";

        $h->db->query($h->db->prepare($sql, $userId, $key));
        
        // Maybe also delete all claims for this user over a certain time period, say 3 months
    }
    
    
    public static function removeAllLogins($h, $userId)
    {
        $sql = "DELETE FROM " . TABLE_USERLOGINS
                . " WHERE user_id = %d";

        $h->db->query($h->db->prepare($sql, $userId));
    }
}
