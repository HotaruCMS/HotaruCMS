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

$this->folder = $this->cage->get->testAlnumLines('plugin'); // get plugin name from url
$this->getPluginName();

/* CSRF protection for all plugin settings pages. We don'T know if plugin developers will 
   use GET or POST in their settings form so we have to check for both... */

$safe = true; // CSRF flag

// check if CSRF key exists - GET METHOD
if ($this->cage->get->keyExists('token')) {
    $csrf = new csrf($hotaru->db);
    $csrf->action = $hotaru->getPagename();
    $safe =  $csrf->checkcsrf($hotaru->cage->get->testAlnum('token'));
}

// check if CSRF key exists - POST METHOD
if ($this->cage->post->keyExists('token')) {
    $csrf = new csrf($hotaru->db);
    $csrf->action = $hotaru->getPagename();
    $safe =  $csrf->checkcsrf($hotaru->cage->post->testAlnum('token'));
}

// set a new CSRF key
$csrf = new csrf($hotaru->db);  
$csrf->action = $hotaru->getPagename();
$csrf->life = 10; 
$hotaru->token = $csrf->csrfkey();

?>

<p class="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a> 
    <?php // only show Admin CP link to users with can_admin_acesss permissions, not can_[plugin]_settings permissions... 
        if ($this->currentUser->getPermission('can_access_admin') == 'yes') { ?>
        &raquo; <a href="<?php echo $hotaru->url(array(), 'admin'); ?>"><?php echo $hotaru->lang["admin_theme_main_admin_cp"]; ?></a> 
    <?php } ?>
    &raquo; <?php echo $hotaru->lang["admin_theme_plugin_settings"]; ?> 
    <?php if ($hotaru->name) { echo "&raquo; " .  $hotaru->name; } ?>
</p>

<div id="plugin_settings">
    <?php 
        if ($safe && $hotaru->folder == "") {
            $hotaru->pluginHook('admin_sidebar_plugin_settings');
        } elseif ($safe) {
            $result = $hotaru->pluginHook('admin_plugin_settings', true, $hotaru->folder); 
            if (!isset($result) || !is_array($result)) {
                $hotaru->message = $hotaru->lang['admin_plugin_settings_inactive'];
                $hotaru->messageType = 'red';    
                $hotaru->showMessage();
            }
        } elseif (!$safe) {
            $hotaru->message = $this->lang['error_csrf'];
            $hotaru->messageType = 'red';
            $hotaru->showMessage();
        }
    ?>
</div>

<?php 

?>
