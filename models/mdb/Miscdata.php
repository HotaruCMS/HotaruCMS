<?php

namespace Hotaru\Models2;

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
    
    public static function exists($h, $key) {
        //$model = self::where('miscdata_key', $key)->pluck('miscdata_value');
        
        $query = "SELECT miscdata_id FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s LIMIT 1";
	$model = $h->db->get_var($h->db->prepare($query, $key));
        
        return $model;
    }
    
    /**
     * Serialized list of current settings
     * 
     * @return type
     */
    public static function getCurrentSettings($h, $key) {
        //$model = self::where('miscdata_key', $key)->pluck('miscdata_value');
        
        $query = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s LIMIT 1";
	$model = $h->db->get_var($h->db->prepare($query, $key));
        
        return $model;
    }
    
    /**
     * Serialized settings with default values
     * @return type
     */
    public static function getUserSettings($h, $type) {
        
        if ($type == 'site') { 
            $field = 'miscdata_value';
        } elseif ($type == 'base') { 
            $field = 'miscdata_default';
        } else { 
            return false;
        }
                
        $query = "SELECT " . $field . " FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s LIMIT 1";
	$sql = $h->db->prepare($query, 'user_settings');
                
        if (isset($h->vars['default_user_settings'][$sql])) { 
                $model = $h->vars['default_user_settings'][$sql]; 
        } else {
                $h->smartCache('on', 'miscdata', 60, $sql); // start using database cache
                $model = $h->db->get_var($sql);
                $h->smartCache('off'); // stop using database cache
        }
        
        return $model;
    }
    
    public static function getAll($h)
    {
            $sql = "SELECT * FROM " . TABLE_MISCDATA; 
            
            $model = $h->db->get_results($sql);
            
            return $model;
    }
    
    
    /**
     * Serilaized list of theme settings
     * 
     * @param type $theme
     * @return type
     */
    public static function getAllThemeSettings($h, $theme)
    {
        //$model = self::where('miscdata_key', $theme . '_settings')->first();
        
            $sql = "SELECT miscdata_value, miscdata_default FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
            $query = $h->db->prepare($sql, $theme . '_settings');
                
            $h->smartCache('on', 'miscdata', 60, $query); // start using cache
            $model = $h->db->get_row($query);
            $h->smartCache('off'); // stop using cache
        
            return $model;
    }
    
    public static function getDefaultValue($h, $setting, $cache = 'on')
    {
            $sql = "SELECT miscdata_default FROM " . TABLE_MISCDATA ." WHERE miscdata_key = %s  LIMIT 1";
            $query = $h->db->prepare($sql, $setting);                

            if ($cache) {
                $h->smartCache('on', 'miscdata_default' . $setting, 60, $query); // start using cache
            }

            $model = $h->db->get_var($query);

            if ($cache) {
                $h->smartCache('off'); // stop using cache
            }

            return $model;
    }
    
    public static function getCurrentValue($h, $setting, $cache = 'on')
    {
            $sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA ." WHERE miscdata_key = %s  LIMIT 1";
            $query = $h->db->prepare($sql, $setting);                

            if ($cache) {
                $h->smartCache('on', 'miscdata_value_' . $setting, 60, $query); // start using cache
            }

            $model = $h->db->get_var($query);

            if ($cache) {
                $h->smartCache('off'); // stop using cache
            }

            return $model;
            
//        $model = self::where('miscdata_key', $setting)->pluck('miscdata_value');
//        return $model;
    }
    
    
    public static function getLatestVersion($h)
    {
        $sql = "SELECT miscdata_id FROM " . TABLE_MISCDATA ." WHERE miscdata_key = %s";
        $model = $h->db->get_row($h->db->prepare($sql, 'hotaru_latest_version'));
                    
        //$model = self::where('miscdata_key', 'hotaru_latest_version')->pluck('miscdata_id');
        return $model;
    }
    
    // scope
    
    
    
    
    // save
    public static function update($h, $settings, $key)
    {
        $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
        
        $h->db->query($h->db->prepare($sql, $settings, $h->currentUser->id, $key));
    }
    
    
    public static function add($h, $settings, $key)
    {
        $sql = "INSERT INTO " . TABLE_MISCDATA . " (miscdata_key, miscdata_value, miscdata_updateby)"
                . "VALUES(%s, %s, %d)";
        
	$h->db->query($h->db->prepare($sql, $key, $settings, $h->currentUser->id));
    }

                        
    public static function updateUserSettingsSiteDefaults($h, $settings)
    {
        $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
        
        $h->db->query($h->db->prepare($sql, $settings, $h->currentUser->id, 'user_settings'));
    }
    
    
    public static function updateUserSettingsBaseDefaults($h, $settings)
    {
        $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_default = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
			
        $h->db->query($h->db->prepare($sql, $settings, $h->currentUser->id, 'user_settings'));
    }
    
}
