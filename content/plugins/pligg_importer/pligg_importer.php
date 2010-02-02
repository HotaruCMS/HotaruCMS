<?php
/**
 * name: Pligg Importer
 * description: Imports and converts a Pligg database to Hotaru CMS
 * version: 0.8
 * folder: pligg_importer
 * class: PliggImporter
 * hooks: admin_plugin_settings, admin_sidebar_plugin_settings, admin_header_include
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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


class PliggImporter
{
    
    /**
     * Pligg Importer page in Admin Settings
     */
    public function admin_plugin_settings($h)
    {
        $pliggimp_path = PLUGINS . "/pligg_importer/";
        
        include_once($pliggimp_path . "pliggimp_categories.php");
        include_once($pliggimp_path . "pliggimp_links.php");
        include_once($pliggimp_path . "pliggimp_comments.php");
        include_once($pliggimp_path . "pliggimp_tags.php");
        include_once($pliggimp_path . "pliggimp_users.php");
        include_once($pliggimp_path . "pliggimp_votes.php");
    
        if ($h->cage->post->testAlpha('submitted'))
        {
            // Save uploaded file and show the result
            $file_name = $this->save_uploaded_file($h);
            $table = $h->cage->post->testAlpha('table');
            $this->upload_result($h, $file_name, $table);
        } 
        elseif ($h->cage->get->keyExists('cleaner'))
        {
            $this->character_cleaner($h);
        }
        elseif ($h->cage->get->keyExists('step'))
        {
            $step = $h->cage->get->testInt('step');
            $file_name = $h->cage->get->sanitizeTags('file_name');
            
            if (!isset($file_name) || !$file_name) { 
                // Go to page 
                $function_name = "page_" . $step;
                
                if ($step == 7) { 
                    $this->$function_name($h);
                } else {
                    $class_name = "PliggImp" . $step; 
                    $classStep = new $class_name();
                    $classStep->$function_name($h);
                }
            } else {
                // Upload file
                $this->process_file($h, $step, $file_name);
            }
        } 
        else
        {
            $this->page_welcome($h);    // Page One - welcome and instructions
        }
        
        return true;
    }
    
    
    /**
     * Page 1 - welcome message, instructions and checks plugins are active
     */
    public function page_welcome($h)
    {
        // FIRST PAGE WITH UPLOAD FORM
        echo "<div id='pliggimp'>";
        echo "<h2>Welcome to the Pligg Importer</h2>";
        echo "<p>This plugin will attempt to import a Pligg or SWCMS site into Hotaru CMS. <b>Be warned, if you have a large website, it can take a long time to do the import and you may exceed various time and size limits on your server</b>. Please read through the <a href='http://hotarucms.org/showpost.php?p=472'>Pligg Importer forum thread</a> for suggestions.</p>";
        
        echo "<table>";
        echo "<tr><td id='before_begin'>Before you begin, you'll need to...</td></tr>";
        
        echo "<tr><td><span class='bold_red'>1.</span> Export the following Pligg database tables as <b>XML files</b>. If using phpMyAdmin, go to each table and click the \"Export\" tab. Then select \"XML\" and save with no compression.: <i>categories, links, comments, tags, users</i> and <i>votes</i></td></tr>";
        
        echo "<tr><td><span class='bold_red'>3.</span> Make sure your Pligg username is at least 4 characters. Learn more about that <a href='http://hotarucms.org/showpost.php?p=472&postcount=9'>here in the forums</a>.</td></tr>";
        
        echo "<tr><td><span class='bold_red'>2.</span> Change your Hotaru profile. So you don't delete yourself when importing the Users data, make sure your Hotaru username and password match your old 'god' account from Pligg. </td></tr>";

        echo "<tr><td><span class='bold_red'>4.</span> Make sure that the \"uploads\" folder in the <i>pligg_importer</i> folder is writable (chmod 777)</td></tr>";

        echo "</table>";
         
        echo "<h2 style='color: green;'>Click \"Import a Pligg Database\" to begin...</b></h2><br />";
        echo "<a class='pliggimp_next' href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer&amp;step=1'>Import a Pligg Database</a><br /><br />";
        
        echo "<h2>Character Cleaner</h2><br />";
        echo "The Character Cleaner should be used <b><i>after</i></b> importing a Pligg database and <b><i>only</i></b> if you are having trouble with strange characters in posts. What it does is simply strip common problem characters from post titles and content. <b>Note: </b> This script may take some time to run depending on the size of your database.";
        echo "<br /><a class='pliggimp_next' href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer&amp;cleaner=1'>Run the Character Cleaner</a>";
        
        echo "</div> <!-- close pliggimp div -->";
    }
    
    
    /**
     * Show the result of the file upload and offer link to continue
     *
     * @param str $file_name
     * @param str $table table name
     */
    public function upload_result($h, $file_name, $table)
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
                echo "<span style='color: green;'><i>" . $file_name . "</i> has been uploaded successfully.</span> <a class='pliggimp_next' href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer&amp;file_name=" . $file_name . "&amp;" . "step=" . $step . "'>Continue</a>";
                
                echo "<h2>Click \"Continue\" to start the import</h2>";
                echo "Please note that large tables with thousands of records may take some time to import, and <b>any existing data will be overwritten.</b>";
            }
            else
            {
                echo "<span style='color: red;'>Import aborted.</span> <a href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer'>Click here</a> to return to the start.";
            }
            echo "</div>";
    }
    
    
    /**
     * Upload and save the file in the plugin's uploads folder
     *
     * @return string|false
     */
    public function save_uploaded_file($h)
    {
        /* *****************************
         * ****************************/
         
         // EDIT THIS TO INCREASE FILE SIZE LIMIT
        $size_limit = 104857600; // 100MB
        
        /* *****************************
         * ****************************/
         
        $tmp_filepath = $h->cage->files->getRaw('/file/tmp_name');
        $file_name = basename($h->cage->files->sanitizeTags('/file/name'));
        $file_type = $h->cage->files->testPage('/file/type');
        $file_size = $h->cage->files->testInt('/file/size');
        $file_error = $h->cage->files->testInt('/file/error');
        $destination = PLUGINS . "pligg_importer/uploads/";
        
        if ($file_type == "text/xml" && $file_size < $size_limit)
        {
            if ($file_error > 0)
            {
                $h->message = "Error: code " . $file_error;
                $h->messageType = "red";
                $h->showMessage();
                return false;
            }
            else
            {
                if (!move_uploaded_file($tmp_filepath, $destination . $file_name)) {
                    $h->message = "Failed to move the file to the pligg_importer uploads folder.";
                    $h->messageType = "red";
                    $h->showMessage();
                    return false;
                }
                
                $h->message = "Uploaded succesfully!";
                $h->messageType = "green";
                $h->showMessage();
                $h->vars['status'] = "uploaded";
                            
                return $file_name;
            }
        }
        else
        {    
            if ($file_type != "text/xml") {
                $h->message = "Invalid file: Must be <i>text/xml</i>";
            } elseif ($file_size >= $size_limit) {
                $h->message = "Invalid file: Exceeded " . 
                    display_filesize($file_size);
            }
            
            $h->messageType = "red";
            $h->showMessage();
            return false;
        }
    }
    
    /**
     * Import data into the database from an XML file
     *
     * @param int $step
     * @param str $file_name
     */
    public function process_file($h, $step = 0, $file_name = '')
    {
        $uploads_folder = PLUGINS . "pligg_importer/uploads/";
        $xml = simplexml_load_file($uploads_folder . $file_name);
    
        echo "<h2>Importing data from <i>" . $xml->getName() . "</i></h2>";
            
        switch($step) {
            case 1:
                $this->create_temp_table($h);
                $cats = new PliggImp1();
                $cats->step1($h, $xml, $file_name);
                break;
            case 2:
                $links = new PliggImp2();
                $links->step2($h, $xml, $file_name);
                break;
            case 3:
                $comms = new PliggImp3();
                $comms->step3($h, $xml, $file_name);
                break;
            case 4:
                $tags = new PliggImp4();
                $tags->step4($h, $xml, $file_name);
                break;
            case 5:
                $users = new PliggImp5();
                $users->step5($h, $xml, $file_name);
                break;
            case 6:
                $votes = new PliggImp6();
                $votes->step6($h, $xml, $file_name);
                break;
            default:
                break;
        }
       
        echo "<br /><br />";
    
    }
    
    
    /**
     * Create a temporary table
     */
    function create_temp_table($h)
    {
        // PLIGGIMP_TEMP TABLE - stores mappings between old and new data.
        
        // Drop and rebuild the table if it already exists
        $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'pliggimp_temp`;';
        $h->db->query($sql);
        
        $sql = "CREATE TABLE `" . DB_PREFIX . "pliggimp_temp` (
          `pliggimp_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `pliggimp_setting` varchar(64) NOT NULL,
          `pliggimp_old_value` int(20) NOT NULL DEFAULT 0,
          `pliggimp_new_value` int(20) NOT NULL DEFAULT 0,
          `pliggimp_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `pliggimp_updateby` int(20) NOT NULL DEFAULT 0
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Pligg Importer Temporary Data';";
    
        $h->db->query($sql);
    }
        
    /**
     * Import complete
     */
    public function page_7($h)
    {
        // Drop the pliggimp_temp table...
        $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'pliggimp_temp`;';
        $h->db->query($sql);
        
        // Delete the uploaded XML files
        $h->deleteFiles(PLUGINS . 'pligg_importer/uploads/');
        
        // Import Complete
        
        $h->message = "Congratulations! You have completed the import.";
        $h->messageType = 'green';
        $h->showMessage();
        
        echo "<div id='pliggimp'>";
        echo "<h2>Pligg Import Complete</h2>";
        echo "<p>Before you take a look at your site, please understand that because Hotaru CMS is different form Pligg in many ways, not everything from your Pligg database could be included. However, most of your content has been imported so your site shouldn't be too disrupted by the changeover. <b>Okay, we're done, go take a look!</b></p>";
        echo "</div>";
    }
    
    
    /**
     * Corrects botched utf-8 content
     */
    function character_cleaner($h)
    {
        $sql = "SELECT post_id, post_title, post_content FROM " . TABLE_POSTS;
        $content = $h->db->get_results($h->db->prepare($sql));
               
        if ($content) {
            foreach ($content as $item) {
                $item->post_title = strip_foreign_characters(urldecode($item->post_title));
                $item->post_content = strip_foreign_characters(urldecode($item->post_content));
                            
                $sql = "UPDATE " . TABLE_POSTS . " SET post_title = %s, post_content = %s WHERE post_id = %d";
                $h->db->query($h->db->prepare($sql, urlencode($item->post_title), urlencode($item->post_content), $item->post_id));
            }
        }
        
        $sql = "SELECT comment_id, comment_content FROM " . TABLE_COMMENTS;
        $content = $h->db->get_results($h->db->prepare($sql));
               
        if ($content) {
            foreach ($content as $item) {
                $item->comment_content = strip_foreign_characters(urldecode($item->comment_content));
                            
                $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_content = %s WHERE comment_id = %d";
                $h->db->query($h->db->prepare($sql, urlencode($item->comment_content), $item->comment_id));
            }
        }
        
        $h->message = "Post titles and descriptions updated";
        $h->messageType = "green";
        $h->showMessage();
    }
}

?>
