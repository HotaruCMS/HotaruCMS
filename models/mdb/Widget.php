<?php

namespace HotaruModels2;

class Widget extends BaseModel
{
    protected $table = 'widgets';
 
    
    // get
    
    public static function getPluginNameFromWidget($h, $widget)
    {
        $sql = "SELECT widget_plugin FROM " . TABLE_WIDGETS . ' WHERE widget_function = %s LIMIT 1';
        $model = $h->db->get_var($h->db->prepare($sql, $widget));
        
        return $model;
    }
}
