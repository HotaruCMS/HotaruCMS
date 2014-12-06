<?php

namespace HotaruModels;

class Pluginhook extends BaseModel
{
        # explicit table name since our table is not "pluginhooks" 
        protected $table =  'pluginhooks';

        # explicit pk since our pk is not "id" 
        protected $primaryKey = 'phook_id';
        
        public function plugin()
        {
            return $this->hasOne('\HotaruModels\Plugin', 'plugin_folder', 'plugin_folder');
        }

        public static function getAllEnabled()
        {
            $model = self::whereHas('plugin', function($query) {
                            $query->where('plugin_enabled', 1);
                        })
                    ->get(['plugin_folder', 'plugin_hook']); //remember(30, 'pluginhook_all')
            
            return $model;
        }
        
        public static function getHooks($hook)
        {
            $model = self::where('plugin_hook', $hook)->get(['plugin_folder', 'plugin_hook']);
            return $model;
        }
}

?>