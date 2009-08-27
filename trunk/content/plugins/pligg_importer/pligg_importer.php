<?php
/**
 * name: Pligg Importer
 * description: Imports and converts a Pligg database to Hotaru CMS
 * version: 0.1
 * folder: pligg_importer
 * prefix: pliggimp
 * requires: category_manager 0.1, categories 0.1, submit 0.1, tags 0.1, users 0.1, vote 0.1
 * hooks: admin_plugin_settings, admin_sidebar_plugin_settings, admin_header_include
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
 * Link to settings page in the Admin sidebar
 */
function pliggimp_admin_sidebar_plugin_settings()
{
    echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'pligg_importer'), 'admin') . "'>Pligg Importer</a></li>";
}


/**
 * Include CSS file
 */
function pliggimp_admin_header_include()
{
    global $admin, $plugin;
    
    $plugin->include_css('pligg_importer');
}


/**
 * Pligg Importer page in Admin Settings
 */
function pliggimp_admin_plugin_settings()
{
    global $cage, $status, $plugin, $cat;
    
    $pliggimp_path = PLUGINS . "/pligg_importer/";
    
    include_once($pliggimp_path . "pliggimp_categories.php");
    include_once($pliggimp_path . "pliggimp_links.php");
    include_once($pliggimp_path . "pliggimp_comments.php");
    include_once($pliggimp_path . "pliggimp_tags.php");
    include_once($pliggimp_path . "pliggimp_users.php");
    include_once($pliggimp_path . "pliggimp_votes.php");

    if ($cage->post->testAlpha('submitted'))
    {
        // Save uploaded file and show the result
        $file_name = pliggimp_save_uploaded_file();
        $table = $cage->post->testAlpha('table');
        pliggimp_upload_result($file_name, $table);
    } 
    elseif ($cage->get->keyExists('cleaner'))
    {
        character_cleaner();
    }
    elseif ($cage->get->keyExists('step'))
    {
        $step = $cage->get->testInt('step');
        $file_name = $cage->get->getMixedString2('file_name');
        
        if (!isset($file_name) || !$file_name) { 
            // Go to page 
            $function_name = "pliggimp_page_" . $step; 
            $function_name();
        } else {
            // Upload file
            pliggimp_process_file($step, $file_name);
        }
    } 
    else
    {
        pliggimp_page_welcome();    // Page One - welcome and instructions
    }

}


/**
 * Page 1 - welcome message, instructions and checks plugins are active
 */
function pliggimp_page_welcome()
{
    global $plugin;
    
    // FIRST PAGE WITH UPLOAD FORM
    echo "<div id='pliggimp'>";
    echo "<h2>Welcome to the Pligg Importer</h2>";
    echo "<p>Before starting, you'll need to complete three steps. First make sure the Hotaru plugins below are installed and active. Second, export the Pligg database tables listed below as <b>XML files</b>. If using phpMyAdmin, go to each table and click the \"Export\" tab. Then select \"XML\" and save with no compression. Third, change your Hotaru username and password to match the 'god' account from your Pligg site.</p>";
    
    echo "<table><tr>";
    echo "<td colspan=3 id='before_begin'>Before you begin, you'll need to...</td></tr>";

    echo "<tr><td>";
    
    echo "<ul id='table_list'>";
    echo "<li class='list_header_red'>ACTIVATE</li>";
    echo "<li class='list_header'>Hotaru plugins</li>";
    echo "<li>Category Manager</li>";
    echo "<li>Categories</li>";
    echo "<li>Submit</li>";
    echo "<li>Comments</li>";
    echo "<li>Tags</li>";
    echo "<li>Users</li>";
    echo "<li>Vote</li>";
    echo "</ul>";
    
    echo "</td><td>";
    
    echo "<ul id='table_list'>";
    echo "<li class='list_header_red'>EXPORT</li>";
    echo "<li class='list_header'>XML files from Pligg</li>";
    echo "<li>categories</li>";
    echo "<li>links</li>";
    echo "<li>comments</li>";
    echo "<li>tags</li>";
    echo "<li>users</li>";
    echo "<li>votes</li>";
    echo "</ul>";
    
    echo "</td><td>";

    echo "<ul id='table_list'>";
    echo "<li class='list_header_red'>CHANGE</li>";
    echo "<li class='list_header'>your Hotaru profile</li>";
    echo "<li>So you don't delete yourself when importing the Users data, make sure your Hotaru username and password match your old 'god' account from Pligg.</li>";
    echo "</ul>";

    echo "</td>";
    echo "</tr>";
    
    echo "<tr><td colspan=3><span style='color: red'><b>IMPORTANT:</b></span> Make sure that the \"uploads\" folder in the <i>pligg_importer</i> folder is writable (chmod 777).</td></tr>";
    echo "</table>";
    
    $error = 0;
    $inactive = "";
    
    if (!$plugin->plugin_active('submit')) {
        $inactive .= "<li style='color: red;'><b>Submit plugin is inactive</b></li>"; 
        $error = 1;
    }
    if (!$plugin->plugin_active('comments')) {
        $inactive .= "<li style='color: red;'><b>Comments plugin is inactive</b></li>"; 
        $error = 1;
    }
    if (!$plugin->plugin_active('category_manager')) {
        $inactive .= "<li style='color: red;'><b>Category Manager plugin is inactive</b></li>"; 
        $error = 1;
    }
    if (!$plugin->plugin_active('categories')) {
        $inactive .= "<li style='color: red;'><b>Categories plugin is inactive</b></li>"; 
        $error = 1;
    }
    if (!$plugin->plugin_active('tags')) {
        $inactive .= "<li style='color: red;'><b>Tags plugin is inactive</b></li>"; 
        $error = 1;
    }
    if (!$plugin->plugin_active('users')) {
        $inactive .= "<li style='color: red;'><b>Users plugin is inactive</b></li>"; 
        $error = 1;
    }
    if (!$plugin->plugin_active('vote')) {
        $inactive .= "<li style='color: red;'><b>Vote plugin is inactive</b></li>"; 
        $error = 1;
    }
     
    if ($error == 0) {
        echo "<h2 style='color: green;'>All plugins are active! Click \"Import a Pligg Database\" to begin...</b></h2><br />";
        echo "<a class='next' href='" . url(array('page'=>'plugin_settings', 'plugin'=>'pligg_importer', 'step'=>1), 'admin') . "'>Import a Pligg Database</a><br /><br />";
    } else {
        echo "<h2>Please fix the following problems:</h2>";
        echo "<ul>";
        echo $inactive;
        echo "</ul>";
    }
    
    echo "";
    echo "<h2>Character Cleaner</h2><br />";
    echo "The Character Cleaner should be used <b><i>after</i></b> importing a Pligg database and <b><i>only</i></b> if you are having trouble with strange characters in posts. What it does is simply strip common problem characters from post titles and content. <b>Note: </b> This script may take some time to run depending on the size of your database.";
    echo "<br /><a class='next' href='" . url(array('page'=>'plugin_settings', 'plugin'=>'pligg_importer', 'cleaner'=>1), 'admin') . "'>Run the Character Cleaner</a>";
    
    echo "</div> <!-- close pliggimp div -->";
}


/**
 * Show the result of the file upload and offer link to continue
 *
 * @param str $file_name
 * @param str $table table name
 */
function pliggimp_upload_result($file_name, $table)
{
        echo "<h2>Upload Results</h2>";
        echo "<div>";
        
        switch($table) {
            case "Categories":
                $step = 1;
                break;
            case "Links":
                $step = 2;
                break;
            case "Comments":
                $step = 3;
                break;
            case "Tags":
                $step = 4;
                break;
            case "Users":
                $step = 5;
                break;
            case "Votes":
                $step = 6;
                break;
            default:
                $step = 0;
                break;
        }
        
        if ($file_name) 
        {
            echo "<span style='color: green;'><i>" . $file_name . "</i> has been uploaded successfully.</span> <a class='next' href='" . url(array('page'=>'plugin_settings', 'plugin'=>'pligg_importer', 'file_name'=>$file_name, 'step'=>$step), 'admin') . "'>Continue</a>";
        }
        else
        {
            echo "<span style='color: red;'>Import aborted.</span> <a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'pligg_importer'), 'admin') . "'>Click here</a> to return to the file upload form.";
        }
        echo "</div>";
        
        echo "<h2>Click \"Continue\" to start the import</h2>";
        echo "Please note that large tables with thousands of records make take some time to import.";
}


/**
 * Upload and save the file in the plugin's uploads folder
 *
 * @return string|false
 */
function pliggimp_save_uploaded_file()
{
    global $cage, $hotaru, $status;
    
    /* *****************************
     * ****************************/
     
     // EDIT THIS TO INCREASE FILE SIZE LIMIT
    $size_limit = 31457280; // 30MB
    
    /* *****************************
     * ****************************/
     
    $tmp_filepath = $cage->files->getRaw('/file/tmp_name');
    $file_name = basename($cage->files->getMixedString2('/file/name'));
    $file_type = $cage->files->testPage('/file/type');
    $file_size = $cage->files->testInt('/file/size');
    $file_error = $cage->files->testInt('/file/error');
    $destination = PLUGINS . "pligg_importer/uploads/";
    
    if ($file_type == "text/xml" && $file_size < $size_limit)
    {
        if ($file_error > 0)
        {
            $hotaru->message = "Error: code " . $file_error;
            $hotaru->message_type = "red";
            $hotaru->show_message();
            return false;
        }
        else
        {
            if (!move_uploaded_file($tmp_filepath, $destination . $file_name)) {
                $hotaru->message = "Failed to move the file to the pligg_importer uploads folder.";
                $hotaru->message_type = "red";
                $hotaru->show_message();
                return false;
            }
            
            $hotaru->message = "Uploaded succesfully!";
            $hotaru->message_type = "green";
            $hotaru->show_message();
            $status = "uploaded";
                        
            return $file_name;
        }
    }
    else
    {    
        if ($file_type != "text/xml") {
            $hotaru->message = "Invalid file: Must be <i>text/xml</i>";
        } elseif ($file_size >= $size_limit) {
            $hotaru->message = "Invalid file: Exceeded " . 
                display_filesize($file_size);
        }
        
        $hotaru->message_type = "red";
        $hotaru->show_message();
        return false;
    }
}

/**
 * Import data into the database from an XML file
 *
 * @param int $step
 * @param str $file_name
 */
function pliggimp_process_file($step = 0, $file_name = '')
{
    global $cage, $current_user, $db;
    
    $uploads_folder = PLUGINS . "pligg_importer/uploads/";
    $xml = simplexml_load_file($uploads_folder . $file_name);

    echo "<h2>Importing data from <i>" . $xml->getName() . "</i></h2>";
        
    switch($step) {
        case 1:
            create_temp_table();
            step1($xml, $file_name);
            break;
        case 2:
            step2($xml, $file_name);
            break;
        case 3:
            step3($xml, $file_name);
            break;
        case 4:
            step4($xml, $file_name);
            break;
        case 5:
            step5($xml, $file_name);
            break;
        case 6:
            step6($xml, $file_name);
            break;
        default:
            break;
    }
   
    echo "<br /><br />";

}


/**
 * Create a temporary table
 */
function create_temp_table()
{
    global $db;
    
    // PLIGGIMP_TEMP TABLE - stores mappings between old and new data.
    
    // Drop and rebuild the table if it already exists
    $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'pliggimp_temp`;';
    $db->query($sql);
    
    $sql = "CREATE TABLE `" . DB_PREFIX . "pliggimp_temp` (
      `pliggimp_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `pliggimp_setting` varchar(64) NOT NULL,
      `pliggimp_old_value` int(20) NOT NULL DEFAULT 0,
      `pliggimp_new_value` int(20) NOT NULL DEFAULT 0,
      `pliggimp_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `pliggimp_updateby` int(20) NOT NULL DEFAULT 0
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Pligg Importer Temporary Data';";

    $db->query($sql);
}
    
/**
 * Import complete
 */
function pliggimp_page_7()
{
    global $hotaru, $db, $admin;
    
    // Drop the pliggimp_temp table...
    $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'pliggimp_temp`;';
    $db->query($sql);
    
    // Delete the uploaded XML files
    $admin->delete_files(PLUGINS . 'pligg_importer/uploads/');
    
    // Import Complete
    
    $hotaru->message = "Congratulations! You have completed the import.";
    $hotaru->message_type = 'green';
    $hotaru->show_message();
    
    echo "<div id='pliggimp'>";
    echo "<h2>Pligg Import Complete</h2>";
    echo "<p>Before you take a look at your site, please understand that because Hotaru CMS is different form Pligg in many ways, not everything from your Pligg database could be included. However, most of your content has been imported so your site shouldn't be too disrupted by the changeover. <b>Okay, we're done, go take a look!</b></p>";
    echo "</div>";
}


/**
 * Corrects botched utf-8 content
 */
function character_cleaner()
{
    global $db, $hotaru;
    
    $sql = "SELECT post_id, post_title, post_content FROM " . TABLE_POSTS;
    $content = $db->get_results($db->prepare($sql));
           
    if ($content) {
        foreach ($content as $item) {
            $item->post_title = strip_foreign_characters(urldecode($item->post_title));
            $item->post_content = strip_foreign_characters(urldecode($item->post_content));
                        
            $sql = "UPDATE " . TABLE_POSTS . " SET post_title = %s, post_content = %s WHERE post_id = %d";
            $db->query($db->prepare($sql, urlencode($item->post_title), urlencode($item->post_content), $item->post_id));
        }
    }
    
    $sql = "SELECT comment_id, comment_content FROM " . TABLE_COMMENTS;
    $content = $db->get_results($db->prepare($sql));
           
    if ($content) {
        foreach ($content as $item) {
            $item->comment_content = strip_foreign_characters(urldecode($item->comment_content));
                        
            $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_content = %s WHERE comment_id = %d";
            $db->query($db->prepare($sql, urlencode($item->comment_content), $item->comment_id));
        }
    }
    
    $hotaru->message = "Post titles and descriptions updated";
    $hotaru->message_type = "green";
    $hotaru->show_message();
}

?>