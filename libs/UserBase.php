<?php
/**
 * Base User functions for basic info, settings and permissions
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class UserBase
{
    protected $id           = 0;
    protected $name         = '';
    protected $role         = 'member';
    protected $password     = 'password';
    protected $email        = '';
    protected $emailValid   = 0;
    protected $loggedIn     = false;
    protected $perms        = array();  // permissions
    protected $settings     = array();  // settings
    protected $profile      = array();  // profile
    protected $ip           = 0;
    protected $lastActivity = 0;
    

    /**
     * Access modifier to set protected properties
     */
    public function __set($var, $val)
    {
        $this->$var = $val;  
    }
    
    
    /**
     * Access modifier to get protected properties
     */
    public function &__get($var)
    {
        return $this->$var;
    }
    
    
    /**
     * Set permission 
     *
     * @param string $perm_name
     * @param mixed $setting
     */
    public function setPermission($perm_name, $setting)
    {
        // don't need options for each user:
        if ($perm_name == 'options') { return false; }
        
        $this->perms[$perm_name] = $setting;
    }
    
    
    /**
     * Set ALL permissions 
     *
     * @param string $setting
     */
    public function setAllPermissions($perms)
    {
        foreach ($perms as $key => $value) {
            $this->setPermission($key, $value);
        }
    }
    
    
    /**
     * Get permission 
     *
     * @param string $perm_name
     * @return mixed 
     */
    public function getPermission($perm_name)
    {
        if (!isset($this->perms[$perm_name])) { 
            return false; 
        } else {
            return $this->perms[$perm_name];
        }
    }
    
    
    /**
     * Get ALL permissions (serialized)
     *
     * @return string 
     */
    public function getAllPermissions()
    {
        return $this->perms;
    }
    
    
    /**
     * Get basic user details
     *
     * @param int $userid 
     * @param string $username
     * @return array|false
     *
     * Note: Needs either userid or username, not both
     */    
    public function getUserBasic($h, $userid = 0, $username = '', $no_cache = false)
    {
        // Prepare SQL
        if ($userid != 0){              // use userid
            $where = "user_id = %d";
            $param = $userid;
        } elseif ($username != '') {    // use username
            $where = "user_username = %s";
            $param = $username;
        } else {
            return false;
        }
        
        // Build SQL
        $query = "SELECT * FROM " . TABLE_USERS . " WHERE " . $where;
        $sql = $h->db->prepare($query, $param);
        
        if (!isset($h->vars['tempUserCache'])) { $h->vars['tempUserCache'] = array(); }

        // If this query has already been read once this page load, we should have it in memory...
        if (!$no_cache && array_key_exists($sql, $h->vars['tempUserCache'])) {
            // Fetch from memory
            $user_info = $h->vars['tempUserCache'][$sql];
        } else {
            // Fetch from database
            $user_info = $h->db->get_row($sql);
            $h->vars['tempUserCache'][$sql] = $user_info;
        }

        if (!$user_info) { return false; }
        
        $this->id = $user_info->user_id;
        $this->name = $user_info->user_username;
        $this->password = $user_info->user_password;
        $this->role = $user_info->user_role;
        $this->email = $user_info->user_email;
        $this->emailValid = $user_info->user_email_valid;
        $this->ip = $user_info->user_ip;
        
        // If a new plugin is installed, we need a way of adding any new default permissions
        // that plugin provides. So, we get all defaults, then overwrite with existing perms.
        
        // get default permissions for the site
        $default_perms = $this->getDefaultPermissions($h, $this->role);
        
        // get existing permissions for the user
        $existing_perms = unserialize($user_info->user_permissions);
        
        // merge permissions
        if (!$default_perms) { $default_perms = array(); }
        if (!$existing_perms) { $existing_perms = array(); }
        $updated_perms = array_merge($default_perms, $existing_perms);
        
        $this->setAllPermissions($updated_perms);
        $user_info->user_permissions = serialize($updated_perms);   // update $user_info
        
        // get user settings:
        $this->settings = $this->getProfileSettingsData($h, 'user_settings', $this->id);
        $user_info->user_settings = $this->settings;   // update $user_info
        
        return $user_info;
    }
    
    
    /**
     * Add a new user
     */
    public function addUserBasic($h)
    {
        // get default permissions
        $permissions = $this->getDefaultPermissions($h, $this->role);

        // get user ip
        $userip = $h->cage->server->testIp('REMOTE_ADDR');
        
        // add user to the database
        $sql = "INSERT INTO " . TABLE_USERS . " (user_username, user_role, user_date, user_password, user_email, user_permissions, user_ip) VALUES (%s, %s, CURRENT_TIMESTAMP, %s, %s, %s, %s)";
        $h->db->query($h->db->prepare($sql, $this->name, $this->role, $this->password, $this->email, serialize($permissions), $userip));
    }
    
    
    /**
     * Update a user
     */
    public function updateUserBasic($h, $userid = 0)
    {
        //determine if the current user is the same as this object's user
        if($userid != $this->id) {
            $updatedby = $userid;
        } else {
            $updatedby = $this->id;
        }
        
        if ($this->id != 0) {
            $sql = "UPDATE " . TABLE_USERS . " SET user_username = %s, user_role = %s, user_password = %s, user_email = %s, user_permissions = %s, user_ip = %s, user_updateby = %d WHERE user_id = %d";
            $h->db->query($h->db->prepare($sql, $this->name, $this->role, $this->password, $this->email, serialize($this->getAllPermissions()), $this->ip, $updatedby, $this->id));
            return true;
        } else {
            return false;
        }
    }
    
    
    /**
     * Default permissions
     *
     * @param string $role or 'all'
     * @param string $field 'site' for site defaults and 'base' for base defaults
     * @param book $options_only returns just the options if true
     * @return array $perms
     */
    public function getDefaultPermissions($h, $role = '', $defaults = 'site', $options_only = false) 
    {
        $perms = array(); // to be filled with default permissions for this user
        
        if ($defaults == 'site') { 
            $field = 'miscdata_value';  // get site permissions
        } else {
            $field = 'miscdata_default'; // get base permissions (i.e. the originals)
        }
        
        // get default permissions from the database:
        $query = "SELECT " . $field . " FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
        $sql = $h->db->prepare($query, 'permissions');
        
        // Create temp cache array
        if (!isset($h->vars['tempPermissionsCache'])) { $h->vars['tempPermissionsCache'] = array(); }
        
        // If this query has already been read once this page load, we should have it in memory...
        if (array_key_exists($sql, $h->vars['tempPermissionsCache'])) {
            // Fetch from memory
            $db_perms = $h->vars['tempPermissionsCache'][$sql];
        } else {
            // Fetch from database
            $db_perms = $h->db->get_var($sql);
            $h->vars['tempPermissionsCache'][$sql] = $db_perms;
        }

        $permissions = unserialize($db_perms);
        
        if (!$permissions) { return array(); } // must return an empty array for array_merge, not false.
        
        if ($options_only) {
            return $permissions['options']; // the editPermissions function in the Users plugin needs these 
        }
        
        if ($role == 'all') { return $permissions; } // plugins need all permissions and options when installed
                
        unset($permissions['options']); // don't need the options anymore
        
        foreach ($permissions as $perm => $roles) { 
            if (isset($roles[$role])) {
                $perms[$perm] = $roles[$role];  // perm for this role
            } else {
                $perms[$perm] = $roles['default']; // default perm because nothing specified for this role
            }
        }
        
        return $perms;
    }
    
    
    /**
     * Update Default permissions
     *
     * @param array $new_perms from a plugin's install function
     * @param string $defaults - either "site", "base" or "both" 
     */
    public function updateDefaultPermissions($h, $new_perms = array(), $defaults = 'both') 
    {
        if (!$new_perms) { return false; }
        
        // get and merge permissions
        if ($defaults == 'site')
        {
            $site_perms = $this->getDefaultPermissions($h,'all', 'site'); //get site defaults
            $site_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s WHERE miscdata_key = %s";
            $h->db->query($h->db->prepare($sql, serialize($site_perms), 'permissions'));
        } 
        elseif ($defaults == 'base')
        {
            $base_perms = $this->getDefaultPermissions($h,'all', 'base'); // get base defaults
            $base_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_default = %s WHERE miscdata_key = %s";
            $h->db->query($h->db->prepare($sql, serialize($base_perms), 'permissions'));
        }
        else 
        {
            $site_perms = $this->getDefaultPermissions($h,'all', 'site'); //get site defaults
            $site_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $base_perms = $this->getDefaultPermissions($h,'all', 'base'); // get base defaults
            $base_perms = array_merge_recursive($base_perms, $new_perms); // merge
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_default = %s WHERE miscdata_key = %s";
            $h->db->query($h->db->prepare($sql, serialize($site_perms), serialize($base_perms), 'permissions'));
        }
    }

    
    /**
     * update permissions in the database
     *
     * @param int $userid
     */
    public function updatePermissions($h)
    {
        $sql = "UPDATE " . TABLE_USERS . " SET user_permissions = %s WHERE user_id = %d";
        $h->db->get_var($h->db->prepare($sql, serialize($this->getAllPermissions()), $this->id));
    }
    
    
    /**
     * Get a user's profile or settings data
     *
     * @return array|false
     */
    public function getProfileSettingsData($h, $type = 'user_profile', $userid = 0, $check_exists_only = false)
    {
        if (!$userid) { $userid = $this->id; }

        $query = "SELECT usermeta_value FROM " . DB_PREFIX . "usermeta WHERE usermeta_userid = %d AND usermeta_key = %s";
        $sql = $h->db->prepare($query, $userid, $type);

        if (isset($h->vars[$sql])) { 
            $result = $h->vars[$sql]; 
        } else {
            $h->smartCache('on', 'usermeta', 60, $sql); // start using database cache
            $result = $h->db->get_var($sql);
            $h->vars[$sql] = $result;    // cache result in memory (saves for just this page load)
            $h->smartCache('off'); // stop using database cache
        }
        
        // if we're only testing to see if the settings exist, return here:
        if($check_exists_only && $result) { return true; }
        if($check_exists_only && !$result) { return false; }
           
        if ($result) { 
            $result = unserialize($result);
            if ($type == 'user_settings') {
                $defaults = $this->getDefaultSettings($h);
                if ($defaults) {
                    $result = array_merge($defaults, $result);
                }
            }
        } elseif ($type == 'user_settings') {
            return $this->getDefaultSettings($h);
        } else {
            return false;
        }
        
        return $result; 
    }


    /**
     * Save a user's profile or settings data
     *
     * @return array|false
     */
    public function saveProfileSettingsData($h, $data = array(), $type = 'user_profile', $userid = 0)
    {
        if (!$data) { return false; }
        if (!$userid) { $userid = $this->id; }

        $result = $h->getProfileSettingsData($type, $userid, true);
        
        if (!$result) {
            $sql = "INSERT INTO " . TABLE_USERMETA . " (usermeta_userid, usermeta_key, usermeta_value, usermeta_updateby) VALUES(%d, %s, %s, %d)";
            $h->db->get_row($h->db->prepare($sql, $userid, $type, serialize($data), $h->currentUser->id));
        } else {
            $sql = "UPDATE " . TABLE_USERMETA . " SET usermeta_value = %s, usermeta_updateby = %d WHERE usermeta_userid = %d AND usermeta_key = %s";
            $h->db->get_row($h->db->prepare($sql, serialize($data), $h->currentUser->id, $userid, $type));
        }
        
        return true;
    }
    

    /**
     * Get the default user settings
     *
     * @param string $type either 'site' or 'base' (base for the originals)
     * @return array
     */
    public function getDefaultSettings($h, $type = 'site')
    {
        if ($type == 'site') { 
            $field = 'miscdata_value'; 
        } elseif ($type == 'base') { 
            $field = 'miscdata_default';
        } else { 
            return false;
        }
        
        $query = "SELECT " . $field . " FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
        $sql = $h->db->prepare($query, 'user_settings');

        if (isset($h->vars['default_user_settings'][$sql])) { 
            $result = $h->vars['default_user_settings'][$sql]; 
        } else {
            $h->smartCache('on', 'miscdata', 60, $sql); // start using database cache
            $result = $h->db->get_var($sql);
            $h->vars['default_user_settings'][$sql] = $result; // cache result in memory for this page load
            $h->smartCache('off'); // stop using database cache
        }
        
        if ($result) {
            return unserialize($result);
        } else {
            return false;
        }
    }
    
    
    /**
     * Update the default user settings
     *
     * @param array $settings 
     * @param string $type either 'site' or 'base' (base for the originals)
     * @return array
     */
    public function updateDefaultSettings($h, $settings, $type = 'site')
    {
        if (!$settings) { return false; } else { $settings = serialize($settings); }

        if ($type == 'site') {
            // update the site defaults
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
            $h->db->query($h->db->prepare($sql, $settings, $h->currentUser->id, 'user_settings'));
        } elseif ($type == 'base') {
            // update the base defaults
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_default = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
            $h->db->query($h->db->prepare($sql, $settings, $h->currentUser->id, 'user_settings'));
        }
    }
    
    
    /**
     * Physically delete this user
     * Note: You should delete all their posts, comments, etc. first
     *
     * @param array $user_id (optional)
     */
    public function deleteUser($h, $user_id = 0) 
    {
        if (!$user_id) { $user_id = $this->id; }
        
        $h->pluginHook('userbase_delete_user', '', array('user_id'=>$user_id));
        
        $sql = "DELETE FROM " . TABLE_USERS . " WHERE user_id = %d";
        $h->db->query($h->db->prepare($sql, $user_id));
        
        $sql = "DELETE FROM " . TABLE_USERMETA . " WHERE usermeta_userid = %d";
        $h->db->query($h->db->prepare($sql, $user_id));
    }
}
