<?php

namespace Hotaru\Models;

class Miscdata extends BaseModel
{
    //Here active use - BaseModel
    public static function boot()
    {
        parent::boot();
    }
    
    protected $table = 'miscdata';
    
    protected $primaryKey = 'miscdata_id';
    
    const CREATED_AT = 'miscdata_updatedts';
    
    // get
    
    /**
     * Serialized settings with default values
     * @return type
     */
    public static function getDefaultSettings($key) {
        $model = self::where('miscdata_key', $key)->pluck('miscdata_default');
        return $model;
    }
    
    /**
     * Serialized list of current settings
     * 
     * @return type
     */
    public static function getCurrentSettings($key) {
        $model = self::where('miscdata_key', $key)->pluck('miscdata_value');
        return $model;
    }
    
    /**
     * Serialized settings with default values
     * @return type
     */
    public static function getUserSettings($type) {
        
        if ($type == 'site') { 
            $field = 'miscdata_value';
        } elseif ($type == 'base') { 
            $field = 'miscdata_default';
        } else { 
            return false;
        }
                
        $model = self::where('miscdata_key', 'user_settings')->pluck($field);
        return $model;
    }
    
    
    /**
     * Serilaized list of theme settings
     * 
     * @param type $theme
     * @return type
     */
    public static function getAllThemeSettings($theme)
    {
        $model = self::where('miscdata_key', $theme . '_settings')->first();
        return $model;
    }
    
    public static function getDefaultValue($setting)
    {
        $model = self::where('miscdata_key', $setting)->pluck('miscdata_default');
        return $model;
    }
    
    public static function getCurrentValue($setting)
    {
        $model = self::where('miscdata_key', $setting)->pluck('miscdata_value');
        return $model;
    }
    
    
    public static function updateLatestVersion()
    {
//        $data = \Hotaru\Models\Miscdata::where('miscdata_key', '=', 'hotaru_latest_version')->first();
//                    
//        
//        // move this check in the model maybe
//                    if (!$data) {
//                        $data = new \Hotaru\Models\Miscdata();
//                        $data->miscdata_key = 'hotaru_latest_version';
//                    }
//                    
//                    $data->miscdata_value = $info['version_string'];
//                    $data->save();
    }
    
    
    // scope
    
}
