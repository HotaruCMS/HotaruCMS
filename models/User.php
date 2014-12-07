<?php

namespace Hotaru\Models;

class User extends BaseModel
{
    protected $table = 'users';
    
    public function posts()
    {
        return $this->hasMany('Post', 'user_id', 'post_author');
    }
    
    public static function getBasicFromUserId($userId)
    {
        $model = self::where('user_id', $userId)->first(array('user_id', 'user_username', 'user_password', 'user_role', 'user_email', 'user_email_valid', 'user_ip', 'user_permissions'));
        return $model;
    }
    
    public static function getBasicFromUsername($username, $condition = '=')
    {
        $model = self::where('user_username', $condition, $username)->first(array('user_id', 'user_username', 'user_password', 'user_role', 'user_email', 'user_email_valid', 'user_ip', 'user_permissions'));
        return $model;
    }
    
    public static function getUserNameFromId($id)
    {
        $model = self::where('user_id', $id)->pluck('user_username');
        return $model;
    }
    
    public static function getUserIdFromName($username)
    {
        $model = self::where('user_username', $username)->pluck('user_id');
        return $model;
    }
    
    public static function getEmailFromId($id)
    {
        $model = self::where('user_id', $id)->pluck('user_email');
        return $model;
    }

    public static function getUserIdFromEmail($email)
    {
        $model = self::where('user_email', $email)->pluck('user_id');
        return $model;
    }
    
    public static function isAdmin($username)
    {
        $model = self::where('user_username', $username)->where('user_role', 'admin')->exists();
        return $model;
    }
    
    public static function getCount($role)
    {
        $model = self::where('user_role',$role)->count();
        return $model;
    }
}
