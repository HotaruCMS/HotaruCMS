<?php

namespace Hotaru\Models;

class Post extends BaseModel
{
    protected $table = 'posts';
    
    protected $primaryKey = 'post_id';
    
    // change these in db
    const CREATED_AT = 'post_updatedts';
    
    
    public function user()
    {
        return $this->hasOne('\Hotaru\Models\User', 'user_id', 'post_author');
    }
    
    public function votes()
    {
        return $this->hasMany('\Hotaru\Models\Postvote', 'vote_post_id', 'post_author');
    }
    
    public function comments()
    {
        return $this->hasMany('\Hotaru\Models\Comment', 'comment_post_id', 'post_author');
    }
    
    // we dont need a tags relation because the tags are serialized in a field of the post table
    // if we try to relate theme here it wont work because they would have to be deserialized first
    
    // we shouldnt need to use this relation as we have the lis of categories in memory from init
    public function category()
    {
        return $this->hasOne('\Hotaru\Models\Category', 'category_id', 'post_category');
    }
    
    // scope
    
    public function scopeActive($query)
    {
        return $query->where('post_status', '<>', 'buried')->where('post_status', '<>', 'pending');
    }
    
    
    // get
    
    public static function getByAuthor($user)
    {
        $model = self::where('post_author', $user)->get();
        return $model;
    }
    
    public static function getWithDetails($postId)
    {
        $model = self::with('user', 'votes', 'comments')->where('post_id', '=', $postId)->first();
        return $model;
    }
    
    public static function getPostsByOrigUrl($url)
    {
        $model = self::where('post_orig_url', $url)->get(array('post_id', 'post_status'));
        return $model;
    }
    
    public static function getFirstPostByOrigUrl($url)
    {
        $model = self::where('post_orig_url', $url)->first();
        return $model;
    }
    
    public static function getPostsByPostUrl($url)
    {
        $model = self::where('post_url', $url)->get(array('post_id', 'post_status'));
        return $model;
    }
    
    public static function getFirstPostByPostUrl($url)
    {
        $model = self::where('post_url', $url)->pluck('post_id');
        return $model;
    }
        
    public static function getPostsByTitle($title)
    {
        $model = self::where('post_title', $title)->get(array('post_id', 'post_status'));
        return $model;
    }
    
    public static function getFirstPostByTitle($title)
    {
        $model = self::where('post_title', $title)->pluck('post_id');
        return $model;
    }
    
    
    // count
    
    public static function countByCategory($category)
    {
            $model = self::where('post_category', $category)->count();
            return $model;
    }
    
    
    // save
    
    public static function updateCommentCount($postId)
    {
            $count = Comment::countByPost($postId);
            $model = self::find($postId);
            $model->post_comments_count = $count;
            $model->save();                        
    }
    
    public static function updateCommentCountBulk()
    {
        
        \Illuminate\Database\Capsule\Manager::update('UPDATE hotaru_posts as p '
                . 'SET post_comments_count = (SELECT count(comment_id) '
                . 'FROM hotaru_comments as c '
                . 'WHERE c.comment_post_id = p.post_id)');
    }
    
    // delete
   
     
    
//    public function scopeTop($query)
//{
//    return $query->orderBy('things.votes', 'desc');
//}
//
//public function scopeYear($query)
//{
//    return $query->whereRaw("things.created_at > STR_TO_DATE('" . Carbon::now()->subYear() . "', '%Y-%m-%d %H:%i:%s')");
//}
//
//public function scopeMonth($query)
//{
//    return $query->whereRaw("things.created_at > STR_TO_DATE('" . Carbon::now()->subMonth() . "', '%Y-%m-%d %H:%i:%s')");
//}
//
//public function scopeWeek($query)
//{
//    return $query->whereRaw("things.created_at > STR_TO_DATE('" . Carbon::now()->subWeek() . "', '%Y-%m-%d %H:%i:%s')");
//}
//
//public function scopeDay($query)
//{
//    return $query->whereRaw("things.created_at > STR_TO_DATE('" . Carbon::now()->subDay() . "', '%Y-%m-%d %H:%i:%s')");
//}
//
//public function scopeJoinUser($query)
//{
//    return $query->join('users', function($join)
//        {
//            $join->on('users.id', '=', 'things.created_by');
//        });
//}
}
