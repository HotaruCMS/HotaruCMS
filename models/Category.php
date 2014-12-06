<?php

namespace HotaruModels;

class Category extends BaseModel
{
    protected $table = 'categories';
    
    public function posts()
    {
            return $this->hasMany('\HotaruModels\Post', 'post_category_id', 'category_id');
    }
    
    // get
    
    public static function getAllOrderForNavBar()
    {
            $model = self::get(['category_id', 'category_name', 'category_safe_name', 'category_parent'])
                    ->sortBy(function($cat) {
                return sprintf('%-12s%s', $cat->category_parent, $cat->category_order);
            });
            return $model;
    }
    
    // count
    
    
    
}
