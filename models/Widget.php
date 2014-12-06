<?php

namespace HotaruModels;

class Widget extends BaseModel
{
    protected $table = 'widgets';
 
    
    // get
    
    public static function getPluginNameFromWidget($widget)
    {
        $model = self::where('widget_function', $widget)->pluck('widget_plugin');
        return $model;
    }
}
