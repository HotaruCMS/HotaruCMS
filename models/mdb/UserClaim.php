<?php

namespace HotaruModels2;

class UserClaim extends BaseModel
{
    protected $table = 'userclaim';
    
    public static function getClaim($h, $userId, $claimValue)
    {
        $query = "SELECT * FROM " . TABLE_USERCLAIMS . " WHERE user_id = %d AND claim_value = %s";
        $sql = $h->db->prepare($query, $userId, $claimValue); 
        $model = $h->db->get_row($sql);
        
        //$model = self::where('user_id', $userId)->first(array('user_id', 'user_username', 'user_password', 'user_role', 'user_email', 'user_email_valid'));
        return $model;
    }
    
    public static function getClaims($h, $userId)
    {
        $query = "SELECT * FROM " . TABLE_USERCLAIMS . " WHERE user_id = %d";
        $sql = $h->db->prepare($query, $userId); 
        $model = $h->db->get_results($sql);
        
        return $model;
    }

    
    // save
  
    public static function addClaim($h, $userId, $claimValue, $claimType = "COOKIE")
    {
        $sql = "INSERT INTO " . TABLE_USERCLAIMS . " (user_id, claim_type, claim_value)"
                . " VALUES (%d, %s, %s)";

        $h->db->query($h->db->prepare($sql, $userId, $claimType, $claimValue));
    }

//    public static function updateClaim($h, $folder, $newStatus, $userId)
//    {
//        $sql = "UPDATE " . TABLE_USERCLAIMS . " SET plugin_enabled = %s, plugin_updateby = %d"
//                . " WHERE (plugin_folder = %s)";
//
//        $h->db->query($h->db->prepare($sql, $newStatus, $userId, $folder));
//    }
    
    
    // delete
    
    public static function removeClaim($h, $userId, $claimValue)
    {
        $sql = "DELETE FROM " . TABLE_USERCLAIMS
                . " WHERE user_id = %d AND claim_value = %s";

        $h->db->query($h->db->prepare($sql, $userId, $claimValue));
        
        // Maybe also delete all claims for this user over a certain time period, say 3 months
    }
    
    
    public static function removeAllClaims($h, $userId)
    {
        $sql = "DELETE FROM " . TABLE_USERCLAIMS
                . " WHERE user_id = %d";

        $h->db->query($h->db->prepare($sql, $userId));
    }
}
