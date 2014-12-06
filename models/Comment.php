<?php

namespace HotaruModels;

class Comment extends BaseModel
{
    protected $table = 'comments';
    
    protected $primaryKey = 'comment_id';
    
    // change these in db
        const CREATED_AT = 'comments_updatedts';
    
    public function post()
    {
        return $this->hasOne('\HotaruModels\Post', 'post_id', 'comment_post_id');
    }
    
    public function user()
    {
        return $this->hasOne('\HotaruModels\User', 'user_id', 'comment_user_id');
    }
    
    
    // get
    
    public static function getWithDetails($postId)
    {
        $model = self::with(array('user', 'post' => function($query)
                {
                    $query->where('post_status', '<>', 'buried')->where('post_status', '<>', 'pending');
                }))
                ->where('comment_status', '=', 'approved')
                ->where('comment_post_id', '=', $postId)
                ->orderBy('comment_date', 'asc')
                ->get();
                
        return $model;
    }
    
    public static function getAllWithDetails($order = 'asc')
    {
        $model = self::with('user', 'post')
                ->where('comment_status', '=', 'approved')
                ->orderBy('comment_date', $order)
                ->get();
        
        return $model;
    }
    
    public static function getAllForUserWithDetails($userId, $order = 'asc')
    {
        $model = self::with('user', 'post')
                ->where('comment_status', '=', 'approved')
                ->where('comment_user_id', '=', $userId)
                ->orderBy('comment_date', $order)
                ->get();
        
        return $model;
    }
    
    
    // count
    
    public static function countByPost($postId)
    {
            $model = self::where('comment_status', 'approved')->where('comment_post_id', $postId)->count();
            return $model;
    }
    
    
    // save
    
    public static function saveNew($postId, $text = '', $userId = '')
    {
//        $model = self::(array('settings_name' => $postId));
//
//        $model->comments_value = $value;
//        $model->comments_updateby = $userId;
//
//          // TODO lets save the commentCount to Post table as well
//
//        $model->save();
    }
}

