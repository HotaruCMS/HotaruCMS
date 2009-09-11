<?php 

/* ******* PLUGIN TEMPLATE ******************************************************************************** 
 * Plugin name: RSS Show
 * Template name: rss_show_settings.php
 * License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

global $hotaru, $plugins, $lang; // don't remove

// Cycle through the RSS feeds, displaying their settings...
$id = 1;
while($settings = unserialize($plugins->pluginSettings('rss_show', 'rss_show_' . $id . '_settings'))) {

    // ************* SET VARIABLES *************
    
    $cache_duration = $settings['rss_show_cache_duration'];
    $max_items = $settings['rss_show_max_items'];
    if($settings['rss_show_cache']) { $checked = "checked"; } else { $checked = ""; }
    if(!$checked) { $display = "style='display:none;'"; } else { $display = ""; } 
    
    if($settings['rss_show_author'] == 'yesauthor') { 
        $authorchecked_yes = "checked"; $authorchecked_no = ""; 
    } else { 
        $authorchecked_yes = ""; $authorchecked_no = "checked";
    }
    
    if($settings['rss_show_date'] == 'yesdate') { 
        $datechecked_yes = "checked"; $datechecked_no = ""; 
    } else { 
        $datechecked_yes = ""; $datechecked_no = "checked";
    }
    
    if($settings['rss_show_content'] == 'none') { 
        $contentnone = "checked"; $contentsummaries = ""; $contentfull = "";
    } elseif($settings['rss_show_content'] == 'summaries') { 
        $contentnone = ""; $contentsummaries = "checked"; $contentfull = "";
    } else {
        $contentnone = ""; $contentsummaries = ""; $contentfull = "checked";
    }
    
    // ************* SHOW FORM *************
?>

    <h1><?php echo $lang["rss_show_settings"]; ?> [ id: <?php echo $id; ?> ]</h1>
    <form name='rss_show_form' action='<?php echo BASEURL ?>admin/admin_index.php' method='get'>
    
    <?php echo $lang["rss_show_feed_url"]; ?> <input type='text' size=60 name='rss_show_feed' value='<?php echo $settings['rss_show_feed']; ?>' /><br /><br />
    
    <?php echo $lang["rss_show_feed_title"]; ?> <input type='text' size=30 name='rss_show_title' value='<?php echo $settings['rss_show_title']; ?>' /><br /><br />
    
    <?php echo $lang["rss_show_cache"]; ?> <input type='checkbox' id='rs_cache' name='rss_show_cache' <?php echo $checked ?> /><br /><br />
    
    <div id='rs_cache_duration' <?php echo $display ?>>
    <?php echo $lang["rss_show_cache_duration"]; ?> 
        <select name='rss_show_cache_duration'>
            
            <?php if($cache_duration) { ?>
                <option value='<?php echo $cache_duration; ?>'><?php echo $cache_duration; ?> <?php echo $lang["rss_show_cache_minutes"]; ?></option> 
            <?php } ?>
            <option value='10'>10 mins</option>
            <option value='30'>30 mins</option>
            <option value='60'>60 mins</option>
        </select><br /><br />
    </div>
    
    <?php echo $lang["rss_show_max_items"]; ?> 
        <select name='rss_show_max_items'>
            
            <?php if($max_items) { ?>
                <option value='<?php echo $max_items; ?>'><?php echo $max_items; ?></option> 
            <?php } ?>
            <option value='5'>5</option>
            <option value='10'>10</option>
            <option value='20'>20</option>
        </select><br /><br />
    
    
    <?php echo $lang["rss_show_show_author"]; ?> &nbsp;&nbsp;<input type='radio' name='rss_show_author' value='yesauthor' <?php echo $authorchecked_yes; ?> /> <?php echo $lang["rss_show_yes"]; ?> &nbsp;&nbsp;
    <input type='radio' name='rss_show_author' value='noauthor' <?php echo $authorchecked_no; ?> /> <?php echo $lang["rss_show_no"]; ?><br /><br />    
    
    <?php echo $lang["rss_show_show_date"]; ?> &nbsp;&nbsp;<input type='radio' name='rss_show_date' value='yesdate' <?php echo $datechecked_yes; ?> /> <?php echo $lang["rss_show_yes"]; ?> &nbsp;&nbsp;
    <input type='radio' name='rss_show_date' value='nodate' <?php echo $datechecked_no; ?> /> <?php echo $lang["rss_show_no"]; ?><br /><br />    
    
    
    <?php echo $lang["rss_show_show_content"]; ?> &nbsp;&nbsp;<input type='radio' name='rss_show_content' value='none' <?php echo $contentnone; ?> /> <?php echo $lang["rss_show_titles_only"]; ?> &nbsp;&nbsp;
    <input type='radio' name='rss_show_content' value='summaries' <?php echo $contentsummaries; ?> /> <?php echo $lang["rss_show_summaries"]; ?> &nbsp;&nbsp;
    <input type='radio' name='rss_show_content' value='full' <?php echo $contentfull; ?> /> <?php echo $lang["rss_show_full"]; ?><br /><br />    
    
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='plugin' value='rss_show' />
    <input type='hidden' name='rss_show_id' value='<?php echo $id ?>' />
    <input type='submit' value='<?php echo $lang["rss_show_save"]; ?>' />
    </form>
    
    <?php $id++; ?>
    
<?php }  // ******************** END OF WHILE LOOP ************* ?>
    
<br />

<a href='<?php echo BASEURL; ?>admin/admin_index.php?page=plugin_settings&amp;plugin=rss_show&amp;action=delete_feed&amp;id=<?php echo ($id-1); ?>' style='color: red;'><?php echo $lang["rss_show_delete"]; ?></a> | <a href='<?php echo BASEURL ?>admin/admin_index.php?page=plugin_settings&amp;plugin=rss_show&amp;action=new_feed&amp;id=<?php echo $id; ?>'><?php echo $lang["rss_show_add"]; ?></a><br /><br />
<div style='padding: 0.8em; line-height: 2.0em; background-color: #f0f0f0; -moz-border-radius: 0.5em;- webkit-border-radius: 0.5em;'>
    <b><?php echo $lang["rss_show_usage"]; ?></b><br />
    <?php echo $lang["rss_show_usage1"]; ?><br />
    <pre>&lt;?php &#36;plugin-&gt;checkActions('rss_show'); ?&gt;</pre><br />
    <?php echo $lang["rss_show_usage2"]; ?><br />
    <pre>&lt;?php &#36;plugin-&gt;checkActions('rss_show', true, '', array(2)); ?&gt;</pre><br />
    <?php echo $lang["rss_show_usage3"]; ?><br />
    <pre>&lt;?php &#36;plugin-&gt;checkActions('rss_show', true, '', array(1, 2)); ?&gt;</pre><br />
</div>
    