<?php

namespace HotaruModels2;

class Comment extends BaseModel
{
    protected $table = 'comments';
    
    protected $primaryKey = 'comment_id';
    
    // change these in db
        const CREATED_AT = 'comment_updatedts';
    
    public function post()
    {
        return $this->hasOne('\HotaruModels\Post', 'post_id', 'comment_post_id');
    }
    
    public function user()
    {
        return $this->hasOne('\HotaruModels\User', 'user_id', 'comment_user_id');
    }
    
    
    // get
    
    public static function getWithDetails($h, $postId)
    {  
        $sql = "SELECT C.*, U.user_username, P.post_title FROM " . TABLE_COMMENTS
                . " AS C LEFT OUTER JOIN " . TABLE_USERS
                . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS
                . " AS P ON C.comment_post_id = P.post_id"
                . " WHERE C.comment_post_id = %d AND C.comment_status = %s AND P.post_status <> %s AND P.post_status <> %s"
                . " ORDER BY C.comment_date " . $order;
                        
        $query = $h->db->prepare($sql, $postId, 'approved', 'buried', 'pending');

        $h->smartCache('on', 'comments', 60, $query); // start using cache
        $model = $h->db->get_results($query);
        $h->smartCache('off'); // stop using cache
                
        return $model;
    }
    
    public static function getWithDetailsForComment($h, $commentId)
    {
        $sql = "SELECT C.*, U.user_username, P.post_title FROM " . TABLE_COMMENTS
                . " AS C LEFT OUTER JOIN " . TABLE_USERS .
                " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS
                . " AS P ON C.comment_post_id = P.post_id "
                . "WHERE C.comment_id = %d";
        
        $query = $h->db->prepare($sql, $commentId);

        $h->smartCache('on', 'comments', 60, $query); // start using cache
        $model = $h->db->get_row($query);
        $h->smartCache('off'); // stop using cache
                
        return $model;
    }
    
    public static function getAllWithDetails($h, $order = 'asc')
    {
        $sql = "SELECT C.*, U.user_username, P.post_title, P.post_url FROM " . TABLE_COMMENTS
                . " AS C LEFT OUTER JOIN " . TABLE_USERS
                . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS
                . " AS P ON C.comment_post_id = P.post_id"
                . " WHERE C.comment_status = %s"
                . " ORDER BY C.comment_date " . $order;
        
        $model = $h->db->prepare($sql, 'approved');
        
        return $model;
    }
    
    public static function getAllForUserWithDetails($h, $userId, $order = 'asc')
    {
        $sql = "SELECT C.*, U.user_username, P.post_title, P.post_url FROM " . TABLE_COMMENTS . " AS C LEFT OUTER JOIN " . TABLE_USERS . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS  . " AS P ON C.comment_post_id = P.post_id WHERE C.comment_status = %s AND C.comment_user_id = %d ORDER BY C.comment_date " . $order;
        $model = $h->db->prepare($sql, 'approved', $userId);
                        
        return $model;
    }
    
    
    // count
    
    public static function countByPost($h, $postId)
    {
            //$model = self::where('comment_status', 'approved')->where('comment_post_id', $postId)->count();
            
            $sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_status = %s";
            $query = $h->db->prepare($sql, $postId, 'approved');

            $h->smartCache('on', 'comments', 60, $query); // start using cache
            $model = $h->db->get_var($query);
            $h->smartCache('off'); // stop using cache	
            
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

