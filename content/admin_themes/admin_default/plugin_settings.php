<?php 
/**
 * Theme name: admin_default
 * Template name: plugin_settings.php
 * Template author: Nick Ramsay
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

$this->plugins->folder = $this->cage->get->testAlnumLines('plugin'); // get plugin name from url
$this->plugins->getPluginName();

/* CSRF protection for all plugin settings pages. We don'T know if plugin developers will 
   use GET or POST in their settings form so we have to check for both... */

$safe = true; // CSRF flag

// check if CSRF key exists - GET METHOD
if ($this->cage->get->keyExists('token')) {
    $csrf = new csrf($admin->db);
    $csrf->action = $admin->hotaru->getPagename();
    $safe =  $csrf->checkcsrf($admin->cage->get->testAlnum('token'));
}

// check if CSRF key exists - POST METHOD
if ($this->cage->post->keyExists('token')) {
    $csrf = new csrf($admin->db);
    $csrf->action = $admin->hotaru->getPagename();
    $safe =  $csrf->checkcsrf($admin->cage->post->testAlnum('token'));
}

// set a new CSRF key
$csrf = new csrf($admin->db);  
$csrf->action = $admin->hotaru->getPagename();
$csrf->life = 10; 
$admin->hotaru->token = $csrf->csrfkey();

?>

<p class="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a> 
    <?php // only show Admin CP link to users with can_admin_acesss permissions, not can_[plugin]_settings permissions... 
        if ($this->current_user->getPermission('can_access_admin') == 'yes') { ?>
        &raquo; <a href="<?php echo $admin->hotaru->url(array(), 'admin'); ?>"><?php echo $admin->lang["admin_theme_main_admin_cp"]; ?></a> 
    <?php } ?>
    &raquo; <?php echo $admin->lang["admin_theme_plugin_settings"]; ?> 
    <?php if ($admin->plugins->name) { echo "&raquo; " .  $admin->plugins->name; } ?>
</p>

<div id="plugin_settings">
    <?php 
        if ($safe && $admin->plugins->folder == "") {
            $admin->plugins->pluginHook('admin_sidebar_plugin_settings');
        } elseif ($safe) {
            $result = $admin->plugins->pluginHook('admin_plugin_settings', true, $admin->plugins->folder); 
            if (!isset($result) || !is_array($result)) {
                $admin->hotaru->message = $admin->lang['admin_plugin_settings_inactive'];
                $admin->hotaru->messageType = 'red';    
                $admin->hotaru->showMessage();
            }
        } elseif (!$safe) {
            $admin->hotaru->message = $this->lang['error_csrf'];
            $admin->hotaru->messageType = 'red';
            $admin->hotaru->showMessage();
        }
    ?>
</div>

<?php 

?>
