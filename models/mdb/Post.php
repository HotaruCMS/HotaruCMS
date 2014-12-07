<?php

namespace Hotaru\Models2;

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
    
    public static function getByAuthor($h, $user)
    {
        //$model = self::where('post_author', $user)->get();
        
//        $sql = "SELECT post_id FROM " . TABLE_POSTS. " WHERE post_author = %d";                 
//                    $results = $h->db->get_results($h->db->prepare($sql, $user_id));
        
        $sql = "SELECT * FROM " . TABLE_POSTS . " WHERE post_author = %s";  
        $model = $h->db->get_results($h->db->prepare($sql, $user));
        //$model = $h->mdb->query($sql, $user);
        
        return $model;
    }
    
    public static function getWithDetails($h, $postId)
    {
        //$model = self::with('user', 'votes', 'comments')->where('post_id', '=', $postId)->first();
        
            $query = "SELECT P.*, U.user_username FROM " . TABLE_POSTS . " AS P LEFT OUTER JOIN " . TABLE_USERS . " AS U ON P.post_author = U.user_id WHERE P.post_id = %d";
//                
//                // old but what about caching
//                if (!MEEKRODB) {
//                    // Build SQL                    
                    $sql = $h->db->prepare($query, $postId);                                
//
//                    // Create temp cache array
                    if (!isset($h->vars['tempPostCache'])) { $h->vars['tempPostCache'] = array(); }

                    // If this query has already been read once this page load, we should have it in memory...
                    if (array_key_exists($sql, $h->vars['tempPostCache'])) {
                            // Fetch from memory
                            $post = $h->vars['tempPostCache'][$sql];
                    } else {
                            // Fetch from database
                            $post = $h->db->get_row($sql);
                            $h->vars['tempPostCache'][$sql] = $post;
                    }
//                } else {
//                    $post = $h->mdb->queryOneRow($query, $post_id);
//                }
//		
		if ($post) { return $post; } else { return false; }
//		
//        $sql = "SELECT P.plugin_folder, H.plugin_hook FROM " . TABLE_PLUGINHOOKS . " AS H"
//                    . " LEFT OUTER JOIN " . TABLE_PLUGINS . " AS P ON H.plugin_folder = P.plugin_folder"
//                    . " AND P.plugin_enabled = %d";
//        $model = $h->mdb->query($sql, 1);
        
 //       return $model;
    }
    
    public static function getPostsByOrigUrl($h, $url)
    {
        $sql = "SELECT post_id, post_status FROM " . TABLE_POSTS . " WHERE post_orig_url = %s";
        $model = $h->db->get_results($h->db->prepare($sql, urlencode($url)));
               
        //$model = $h->mdb->queryObj($sql, urlencode($url));
                    
        //$model = self::where('post_orig_url', $url)->get(array('post_id', 'post_status'));
        return $model;
    }
    
    public static function getFirstPostByOrigUrl($h, $url)
    {
        $sql = "SELECT * FROM " . TABLE_POSTS . " WHERE post_orig_url = %s LIMIT 1";
                                
        $model = $h->db->get_row($h->db->prepare($sql, urlencode($url)));
               
        //$model = $h->mdb->queryFirstRow($sql, urlencode($url));
                    
        //$model = self::where('post_orig_url', $url)->first();
        return $model;
    }
    
    public static function getPostsByPostUrl($url)
    {
        $model = self::where('post_url', $url)->get(array('post_id', 'post_status'));
        return $model;
    }
    
    public static function getFirstPostByPostUrl($h, $url)
    {
        $sql = "SELECT post_id FROM " . TABLE_POSTS . " WHERE post_url = %s LIMIT 1";
                                           
        $model = $h->db->get_var($h->db->prepare($sql, urlencode($url)));

        //$model = $h->mdb->queryFirstField($sql, urlencode($post_url));
        //$model = self::where('post_url', $url)->pluck('post_id');
        return $model;
    }
        
    public static function getPostsByTitle($h, $title)
    {
        $sql = "SELECT post_id, post_status FROM " . TABLE_POSTS . " WHERE post_title = %s";                
                               
        $model = $h->db->get_results($h->db->prepare($sql, urlencode($title)));

        //$model = $h->mdb->queryObj($sql, urlencode($title));
                    
        //$model = self::where('post_title', $title)->get(array('post_id', 'post_status'));
        return $model;
    }
    
    public static function getFirstPostByTitle($h, $title)
    {
        $sql = "SELECT post_id FROM " . TABLE_POSTS . " WHERE post_title = %s LIMIT 1";
                      
        $model = $h->db->get_var($h->db->prepare($sql, urlencode($title)));

        //$model = $h->mdb->queryFirstField($sql, urlencode($title));
                    
        //$model = self::where('post_title', $title)->pluck('post_id');
        return $model;
    }
    
    
    // count
    
    public static function countByCategory($h, $category)
    {
            $sql = "SELECT count(post_id) FROM " . TABLE_POSTS . " WHERE post_category = %s";  
            $model = $h->db->get_results($h->db->prepare($sql, $category));
                    
            return $model;
    }
    
    
    // save
    
    public static function updateCommentCount($h, $postId)
    {            
            $count = Comment::countByPost($h, $postId);
            
            $sql = "UPDATE " . TABLE_POSTS . " SET post_comments_count = %d WHERE post_id = %d";
            $model = $h->db->get_results($h->db->prepare($sql, $count, $postId));
    }
    
    public static function updateCommentCountBulk($h)
    {
        $sql = "UPDATE " . TABLE_POSTS . " as p"
                . " SET post_comments_count = (SELECT count(comment_id)"
                . 'FROM ' . TABLE_COMMENTS . ' as c '
                . 'WHERE c.comment_post_id = p.post_id)';
        
        $model = $h->db->get_results($sql);
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
