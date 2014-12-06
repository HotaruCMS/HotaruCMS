<?php

namespace HotaruModels;

class PuginSetting extends BaseModel
{
        protected $table = 'pluginsettings';

        # explicit pk since our pk is not "id" 
        protected $primaryKey = 'phook_id';
        
        public function plugin()
        {
            return $this->hasOne('\HotaruModels\Plugin', 'plugin_folder', 'plugin_folder');
        }
        
        // get
        
        public static function getAll()
        {
            $model = self::get(['plugin_folder', 'plugin_setting', 'plugin_value']);
            return $model;
        }
        
        public static function getAllWhereEnabled()
        {
            $model = self::whereHas('plugin', function($query) {
                            $query->where('plugin_enabled', 1);
                        })
                        ->get(['plugin_folder', 'plugin_setting', 'plugin_value']);
            return $model;
        }
}
    