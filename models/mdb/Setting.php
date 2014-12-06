<?php

namespace HotaruModels2;

class Setting extends BaseModel
{
    protected $table = 'settings';
    
    // i think the primary keys should be changed to just id
    protected $primaryKey = 'settings_id';
    
    // change these in db
    const CREATED_AT = 'settings_updatedts';
    
    public static function getValues($h)
    {
        $sql = "SELECT settings_name, settings_value FROM " . TABLE_SETTINGS;
        //$model = $h->mdb->queryObj($sql);  <-- this added an extra 0.01 to the time because of object
        $model = $h->db->get_results($h->db->prepare($sql));
        
        return $model;
    }
    
    public static function getAll($h)
    {
        $sql = "SELECT settings_name, settings_type, settings_subType, settings_value, settings_default, settings_note, settings_show FROM " . TABLE_SETTINGS;
        $model = $h->db->get_results($h->db->prepare($sql));
        
        return $model;
    }
    
    public static function isSetting($h, $setting)
    {
        //$model = self::where('settings_name', $setting)->exists();
//        $sql = "SELECT settings_name WHERE settings_name = %s FROM " . TABLE_SETTINGS;
//        $model = $h->mdb->query($sql, $setting);
        
        $sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
        $model = $h->db->get_var($h->db->prepare($sql, $setting));
        
        $return = $model ? true : false;
        
        return $return;
    }
    
    public static function makeUpdate($h, $setting, $value = '', $userId = '')
    {
//        $model = Setting::firstOrNew(array('settings_name' => $setting));
//
//        $model->settings_value = $value;
//        $model->settings_updateby = $userId;
        
        //$model->save();
        
        $exists = self::isSetting($h, $setting);
		
        if (!$exists) {
                $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_updateby) VALUES (%s, %s, %d)";
                $h->db->query($h->db->prepare($sql, $setting, $value, $h->currentUser->id));
        } else {
                $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s, settings_value = %s, settings_updateby = %d WHERE (settings_name = %s)";
                $h->db->query($h->db->prepare($sql, $setting, $value, $h->currentUser->id, $setting));
        }
    }
    
    public static function getPluginSettings($h)
    {
        $sql = "SELECT DISTINCT plugin_folder FROM " . DB_PREFIX . "pluginsettings";
        $results = $h->db->get_results($h->db->prepare($sql));
        
        return $results;
    }
}
