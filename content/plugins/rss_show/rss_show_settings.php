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

class RssShowSettings extends RssShow
{
    public function settings()
    {
    // Cycle through the RSS feeds, displaying their settings...
    $id = 1;
    while($settings = unserialize($this->getSetting('rss_show_' . $id . '_settings', 'rss_show'))) {
    
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
   
    
        echo "<h1>" . $this->hotaru->lang["rss_show_settings"] . " [ id:" .  $id . " ]</h1>";
        echo "<form name='rss_show_form' action='" . BASEURL . "admin_index.php' method='get'>";
        
        echo $this->hotaru->lang["rss_show_feed_url"] . " <input type='text' size=60 name='rss_show_feed' value='" . $settings['rss_show_feed'] . "' /><br /><br />";
        
        echo $this->hotaru->lang["rss_show_feed_title"] . " <input type='text' size=30 name='rss_show_title' value='" . $settings['rss_show_title'] . "' /><br /><br />";
        
        echo $this->hotaru->lang["rss_show_cache"] . " <input type='checkbox' id='rs_cache' name='rss_show_cache' " . $checked . " /><br /><br />";
        
        echo "<div id='rs_cache_duration' " . $display . ">";
        echo $this->hotaru->lang["rss_show_cache_duration"]; 
            echo " <select name='rss_show_cache_duration'>";
                
                if($cache_duration) {
                    echo "<option value='" . $cache_duration . "'>" . $cache_duration . " " . $this->hotaru->lang["rss_show_cache_minutes"] . "</option>"; 
                }
                echo "<option value='10'>10 mins</option>";
                echo "<option value='30'>30 mins</option>";
                echo "<option value='60'>60 mins</option>";
            echo "</select><br /><br />";
        echo "</div>";
        
        echo $this->hotaru->lang["rss_show_max_items"]; 
            echo " <select name='rss_show_max_items'>";
                
                if($max_items) {
                    echo "<option value='" . $max_items . "'>" . $max_items . "</option>";
                }
                echo "<option value='5'>5</option>";
                echo "<option value='10'>10</option>";
                echo "<option value='20'>20</option>";
            echo "</select><br /><br />";
        
        
        echo $this->hotaru->lang["rss_show_show_author"] . " &nbsp;&nbsp;<input type='radio' name='rss_show_author' value='yesauthor' " . $authorchecked_yes . " /> " . $this->hotaru->lang["rss_show_yes"] . " &nbsp;&nbsp;";
        echo "<input type='radio' name='rss_show_author' value='noauthor' " . $authorchecked_no . " /> " . $this->hotaru->lang["rss_show_no"] . "<br /><br />";
        
        echo $this->hotaru->lang["rss_show_show_date"] . " &nbsp;&nbsp;<input type='radio' name='rss_show_date' value='yesdate' " . $datechecked_yes . " /> " . $this->hotaru->lang["rss_show_yes"] . " &nbsp;&nbsp;";
        echo "<input type='radio' name='rss_show_date' value='nodate' " . $datechecked_no . " /> " . $this->hotaru->lang["rss_show_no"] . "<br /><br />";
        
        
        echo $this->hotaru->lang["rss_show_show_content"] . " &nbsp;&nbsp;<input type='radio' name='rss_show_content' value='none' " . $contentnone . " /> " . $this->hotaru->lang["rss_show_titles_only"] . " &nbsp;&nbsp;";
        echo "<input type='radio' name='rss_show_content' value='summaries' " . $contentsummaries . " /> " . $this->hotaru->lang["rss_show_summaries"] . " &nbsp;&nbsp;";
        echo "<input type='radio' name='rss_show_content' value='full' " . $contentfull . " /> " . $this->hotaru->lang["rss_show_full"] . "<br /><br />";
        
        echo "<input type='hidden' name='page' value='plugin_settings' />";
        echo "<input type='hidden' name='plugin' value='rss_show' />";
        echo "<input type='hidden' name='rss_show_id' value='$id' />";
        echo "<input type='submit' value='" . $this->hotaru->lang["rss_show_save"] . "' />";
        echo "<input type='hidden' name='token' value='" . $this->hotaru->token . "' />\n";
        echo "</form>";
        
        $id++;
        
    }  // ******************** END OF WHILE LOOP *************
        
    echo "<br />";
    
    echo "<a href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=rss_show&amp;action=delete_feed&amp;id=" . ($id-1) . "' style='color: red;'>" . $this->hotaru->lang["rss_show_delete"] . "</a> | <a href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=rss_show&amp;action=new_feed&amp;id=" . ($id) . "'>" . $this->hotaru->lang["rss_show_add"]  ."</a><br /><br />";
    echo "<div style='padding: 0.8em; line-height: 2.0em; background-color: #f0f0f0; -moz-border-radius: 0.5em;- webkit-border-radius: 0.5em;'>";
        echo "<b>" . $this->hotaru->lang["rss_show_usage"] . "</b><br />";
        echo $this->hotaru->lang["rss_show_usage1"] . "<br />";
        echo "<pre>&lt;?php &#36;hotaru-&gt;plugins-&gt;pluginHook('rss_show'); ?&gt;</pre><br />";
        echo $this->hotaru->lang["rss_show_usage2"] . "<br />";
        echo "<pre>&lt;?php &#36;hotaru-&gt;plugins-&gt;pluginHook('rss_show', true, '', array(2)); ?&gt;</pre><br />";
        echo $this->hotaru->lang["rss_show_usage3"] . "<br />";
        echo "<pre>&lt;?php &#36;hotaru-&gt;plugins-&gt;pluginHook('rss_show', true, '', array(1, 2)); ?&gt;</pre><br />";
    echo "</div>";
    }
}
?>