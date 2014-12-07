<?php

namespace Hotaru\Models2;

class Tag extends BaseModel
{
    protected $table = 'tags';
    
    public function posts()
    {
        return $this->hasMany('\Hotaru\Models\Post', 'post_tag_id', 'tag_id');
    }
    
    
    //get
    
    public static function getTagCloudWords()
    {
//        $model = self::whereHas('posts', function($query) {
//                            $query->where('post_status', 'new');   // or top
//                        })
//                    ->having('tags_archived', 'N')
//                    ->groupBy('tags_word')
//                    ->orderBy(DB::raw('COUNT(tag_id)','desc'))
//                    ->get(['tags_word', DB::raw('COUNT(tag_id) as CNT')]); 
//            
//            return $model;
    }
//    SELECT tags_word, COUNT(tags_word) AS CNT FROM " . TABLE_TAGS . ", " . TABLE_POSTS;
//        $sql .= " WHERE tags_archived = %s AND (tags_post_id = post_id) AND";
//        $sql .= " (post_status = %s || post_status = %s)";
//	$sql .= " GROUP BY tags_word ORDER BY CNT DESC LIMIT 
    
}
