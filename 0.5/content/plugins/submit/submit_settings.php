<?php
/**
 *  File: /plugins/submit/submit_settings.php
 * Admin settings for the Submit plugin
 *  Notes: This file is part of the Submit plugin. The main file is /plugins/submit/submit.php
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
 
 /**
 * Admin settings for the Submit plugin
 */
function sub_settings()
{
    global $hotaru, $plugin, $cage, $lang;
    
    // If the form has been submitted, go and save the data...
    if ($cage->post->getAlpha('submitted') == 'true') { 
        sub_save_settings(); 
    }    
    
    echo "<h1>" . $lang["submit_settings_header"] . "</h1>\n";
    
    // Get settings from database if they exist...
    $enabled = $plugin->plugin_settings('submit', 'submit_enabled');
    $author = $plugin->plugin_settings('submit', 'submit_author');
    $date = $plugin->plugin_settings('submit', 'submit_date');
    $content = $plugin->plugin_settings('submit', 'submit_content');
    $content_length = $plugin->plugin_settings('submit', 'submit_content_length');
    $summary = $plugin->plugin_settings('submit', 'submit_summary');
    $summary_length = $plugin->plugin_settings('submit', 'submit_summary_length');
    $posts_per_page = $plugin->plugin_settings('submit', 'submit_posts_per_page');
    $allowable_tags = $plugin->plugin_settings('submit', 'submit_allowable_tags');

    $plugin->check_actions('submit_settings_get_values');
    
    //...otherwise set to blank:
    if (!$enabled) { $enabled = ''; }
    if (!$author) { $author = ''; }
    if (!$date) { $date = ''; }
    if (!$content) { $content = ''; }
    if (!$content_length) { $content_length = ''; }
    if (!$summary) { $summary = ''; }
    if (!$summary_length) { $summary_length = ''; }
    
    echo "<form name='submit_settings_form' action='" . BASEURL . "admin/admin_index.php?page=plugin_settings&amp;plugin=submit' method='post'>\n";
    
    echo "<p>" . $lang["submit_settings_instructions"] . "</p><br />";
    
    echo "<input type='checkbox' name='enabled' value='enabled' " . $enabled . " >&nbsp;&nbsp;" . $lang["submit_settings_enable"] . "<br />\n";    
    echo "<input type='checkbox' name='title' value='title' checked disabled>&nbsp;&nbsp;" . $lang["submit_settings_title"] . "<br />\n";
    echo "<input type='checkbox' name='author' value='author' " . $author . ">&nbsp;&nbsp;" . $lang["submit_settings_author"] . "<br />\n";
    echo "<input type='checkbox' name='date' value='date' " . $date . ">&nbsp;&nbsp;" . $lang["submit_settings_date"] . "<br />\n";
    echo "<input type='checkbox' name='content' value='content' " . $content . ">&nbsp;&nbsp;" . $lang["submit_settings_content"];
    echo "&nbsp;&nbsp;&nbsp;&nbsp;";
    echo $lang["submit_settings_content_min_length"] . ": <input type='text' size=5 name='content_length' value='" . $content_length . "' /><br />\n";
    echo "<input type='checkbox' name='summary' value='summary' " . $summary . ">&nbsp;&nbsp;" . $lang["submit_settings_summary"];
    echo "&nbsp;&nbsp;&nbsp;&nbsp;";
    echo $lang["submit_settings_summary_max_length"] . ": <input type='text' size=5 name='summary_length' value='" . $summary_length . "' />\n";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;";
    echo $lang["submit_settings_summary_instruct"] . "<br />\n";

    $plugin->check_actions('submit_settings_form');
    
    echo "<br /><input type='text' size=5 name='posts_per_page' value='" . $posts_per_page . "' /> ";
    echo $lang["submit_settings_posts_per_page"] . "<br /><br />\n";
    
    echo $lang["submit_settings_allowable_tags"] . " <input type='text' size=40 name='allowable_tags' value='" . $allowable_tags . "' /><br />";
    echo $lang["submit_settings_allowable_tags_example"] . "\n";

    echo "<br /><br />\n";    
    echo "<input type='hidden' name='submitted' value='true' />\n";
    echo "<input type='submit' value='" . $lang["submit_settings_save"] . "' />\n";
    echo "</form>\n";
}


/**
 * Save Submit Settings
 */
function sub_save_settings() 
{
    global $cage, $hotaru, $plugin, $post, $lang;

    // Enabled
    if ($cage->post->keyExists('enabled')) { 
        $enabled = 'checked'; 
        $post->use_submission = true;
    } else { 
        $enabled = ''; 
        $post->use_submission = false;
    }
    
    // Author
    if ($cage->post->keyExists('author')) { 
        $author = 'checked'; 
        $post->use_author = true;
    } else { 
        $author = ''; 
        $post->use_author = false;
    }
    
    // Date
    if ($cage->post->keyExists('date')) { 
        $date = 'checked'; 
        $post->use_date = true;
    } else { 
        $date = ''; 
        $post->use_date = false;
    }
    
    // Description
    if ($cage->post->keyExists('content')) { 
        $content = 'checked'; 
        $post->use_content = true;
    } else { 
        $content = ''; 
        $post->use_content = false;
    }
    
    // Description length
    if ($cage->post->keyExists('content_length')) { 
        $content_length = $cage->post->getInt('content_length'); 
        if (empty($content_length)) { $content_length = $post->post_content_length; }
    } else { 
        $content_length = $post->post_content_length; 
    } 
    
    // Summary
    if ($cage->post->keyExists('summary')) { 
        $summary = 'checked'; 
        $post->use_summary = true;
    } else { 
        $summary = ''; 
        $post->use_summary = false;
    }
    
    // Summary length
    if ($cage->post->keyExists('summary_length')) { 
        $summary_length = $cage->post->getInt('summary_length'); 
        if (empty($summary_length)) { $summary_length = $post->post_summary_length; }
    } else { 
        $summary_length = $post->post_summary_length; 
    } 
    
    // Posts per page
    if ($cage->post->keyExists('posts_per_page')) { 
        $posts_per_page = $cage->post->testInt('posts_per_page'); 
        if (empty($posts_per_page) || $posts_per_page == 0) { $posts_per_page = $post->$posts_per_page; }
    } else { 
        $posts_per_page = $post->$posts_per_page; 
    } 
    
    // Allowable tags
    if ($cage->post->keyExists('allowable_tags')) { 
        $allowable_tags = $cage->post->getRaw('allowable_tags'); 
        if (empty($allowable_tags)) { $allowable_tags = $post->$allowable_tags; }
    } else { 
        $allowable_tags = $post->$allowable_tags; 
    }
    
    $plugin->check_actions('submit_save_settings');
    
    $plugin->plugin_settings_update('submit', 'submit_enabled', $enabled);
    $plugin->plugin_settings_update('submit', 'submit_author', $author);    
    $plugin->plugin_settings_update('submit', 'submit_date', $date);    
    $plugin->plugin_settings_update('submit', 'submit_content', $content);    
    $plugin->plugin_settings_update('submit', 'submit_content_length', $content_length);    
    $plugin->plugin_settings_update('submit', 'submit_summary', $summary);    
    $plugin->plugin_settings_update('submit', 'submit_summary_length', $summary_length);
    $plugin->plugin_settings_update('submit', 'submit_posts_per_page', $posts_per_page);
    $plugin->plugin_settings_update('submit', 'submit_allowable_tags', $allowable_tags);
    
    $hotaru->message = $lang["submit_settings_saved"];
    $hotaru->message_type = "green";
    $hotaru->show_message();
    
    return true;    
}
?>