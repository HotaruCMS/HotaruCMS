<?php

namespace Hotaru\Models;

class Setting extends BaseModel
{
    protected $table = 'settings';
    
    // i think the primary keys should be changed to just id
    protected $primaryKey = 'settings_id';
    
    // guarded columns in db on mass assignment like when using firstOrCreate
    protected $guarded = array('settings_default');
    
    // change these in db
    const CREATED_AT = 'settings_updatedts';
    
    public static function getValues($h)
    {
        $sql = "SELECT settings_name, settings_value FROM " . TABLE_SETTINGS;
        $model = $h->db->get_results($h->db->prepare($sql));
        
        return $model;
    }
    
    public static function isSetting($h, $setting)
    {
        $model = self::where('settings_name', $setting)->exists();
        return $model;
    }
    
    public static function makeUpdate($setting, $value = '', $userId = '')
    {
        $model = Setting::firstOrNew(array('settings_name' => $setting));

        $model->settings_value = $value;
        $model->settings_updateby = $userId;

        $model->save();
    }
}
