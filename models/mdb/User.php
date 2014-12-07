<?php

namespace Hotaru\Models2;

class User extends BaseModel
{
    protected $table = 'users';
    
    public function posts()
    {
        return $this->hasMany('Post', 'user_id', 'post_author');
    }
    
    public static function getBasicFromUserId($h, $userId)
    {
        $query = "SELECT user_id, user_username, user_password, password_version, user_role, user_email, user_email_valid, user_ip, user_permissions FROM " . TABLE_USERS . " WHERE user_id = %d";
        $sql = $h->db->prepare($query, $userId); 
        $model = $h->db->get_row($sql);
        
        //$model = self::where('user_id', $userId)->first(array('user_id', 'user_username', 'user_password', 'user_role', 'user_email', 'user_email_valid'));
        return $model;
    }
    
    public static function getBasicFromUsername($h, $username, $condition = '=')
    {
        $query = "SELECT user_id, user_username, user_password, password_version, user_role, user_email, user_email_valid, user_ip, user_permissions FROM " . TABLE_USERS . " WHERE user_username " . $condition . " %s";
        $sql = $h->db->prepare($query, $username); 
        $model = $h->db->get_row($sql);
        
        return $model;
    }
    
    public static function getUserNameFromId($h, $id)
    {
        $sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_id = %d LIMIT 1";
	$model = $h->db->get_var($h->db->prepare($sql, $id));
        
        return $model;
    }
    
    public static function getUserIdFromName($h, $username)
    {
        $sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_username = %s  LIMIT 1";
        $model = $h->db->get_var($h->db->prepare($sql, $username));
        
        return $model;
    }
    
    public static function getEmailFromId($h, $id)
    {
        $sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_id = %d  LIMIT 1";
	$model = $h->db->get_var($h->db->prepare($sql, $id));

        return $model;
    }

    public static function getUserIdFromEmail($h, $email)
    {
        $sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_email = %s  LIMIT 1";
	$model = $h->db->get_var($h->db->prepare($sql, $email));

        return $model;
    }
    
    public static function isAdmin($h, $username)
    {
        $sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_username = %s AND user_role = %s  LIMIT 1";
	$model = $h->db->get_row($h->db->prepare($sql, $username, 'admin'));
        
        //$model = self::where('user_username', $username)->where('user_role', 'admin')->exists();
        return $model;
    }
    
    public static function isLockedOut($h, $username)
    {
        $query = "SELECT user_is_locked_out FROM " . TABLE_USERS . " WHERE user_username = %d";
        $sql = $h->db->prepare($query, $username); 
        $model = $h->db->get_var($sql);
        
        return $model;
    }
    
    public static function getCount($h, $role)
    {
        $sql = "SELECT COUNT(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
        $model = $h->db->get_var($h->db->prepare($sql, $role));
        
        return $model;
    }
}
