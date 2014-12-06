<?php

namespace HotaruModels2;

class Category extends BaseModel
{
    protected $table = 'categories';    
    
    // get
    
    public static function getAllOrderForNavBar($h)
    {
            $sql = "SELECT category_id, category_name, category_safe_name, category_parent FROM " . TABLE_CATEGORIES
                    . " ORDER BY category_parent, category_order";            
            //$model = $h->mdb->queryObj($sql);
            $model = $h->db->get_results($sql); 
            
            return $model;
    }
    
    // count
    
    
    
}
