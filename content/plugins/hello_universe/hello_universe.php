<?php
/**
 * name: Hello Universe
 * description: Demonstrates how to make plugins
 * version: 0.6
 * folder: hello_universe
 * class: HelloUniverse
 * hooks: theme_index_top, theme_index_main, theme_index_sidebar
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

class HelloUniverse
{
    public $hotaru = '';   // access Hotaru functions using $this->hotaru
    
    /**
     * FUNCTION #1
     *
     * Function: theme_index_top
     * Purpose: Set the page title on the default page (which is otherwise "page not found")
     * Notes: Uses theme_index_top in the hooks list at the top of this file.
    */
    public function theme_index_top()
    {
        if (!$this->hotaru->pageName) { 
            $this->hotaru->pageTitle = $this->hotaru->lang["hello_universe"];
        }
    }
    
    
    /**
     * FUNCTION #2
     *
     * Function: theme_index_main
     * Purpose: Plugin welcome page with various options.
     * Notes: Uses theme_index_main in the hooks list at the top of this file.
    */
    public function theme_index_main()
    {
        switch ($this->hotaru->pageName) {
            case 'plugin_template':
                $this->hotaru->displayTemplate('plugin_template'); // Displays the page from this plugin folder
                return true;
                break;
            case 'form_example':
                $this->checkSentForm();    // Function #5
                $this->hotaru->displayTemplate('form_example'); // Displays the page from this plugin folder
                return true;
                break;
            default:
                $this->main_page();
                return true;
                break;
        }
    }
    
    
    /**
     * FUNCTION #3
     *
     * Function: main_page
     * Purpose: Output text for the main page.
     * Notes: No hooks. Called by Function #1
     */
    public function main_page()
    {
        // Display output
        echo "<div id='hello_universe' style='margin: 1.0em; background-color: #eee;'>";
        echo "<h2>" . $this->hotaru->lang["hello_universe"] . "</h2>";
        echo "<p>" . $this->hotaru->lang["hello_universe_explanation"] . "</p>";  
        echo "<ul>";
        // Note these links allow for either friendly or unfriendly urls...
        echo "<li><a href='" . $this->hotaru->url(array('page'=>'plugin_template')) . "'>" . $this->hotaru->lang["hello_universe_see_page"] . "</a></li>";
        echo "<li><a href='" . $this->hotaru->url(array('page'=>'form_example')) . "'>" . $this->hotaru->lang["hello_universe_see_form"] . "</a></li>";
        echo "</ul></div>";
    }
    
    /**
     * FUNCTION #4
     *
     * Function: theme_index_sidebar
     * Purpose: A sidebar that overrides the real sidebar!
     * Notes: Uses theme_index_sidebar in the hooks list at the top of this file.
     */
    public function theme_index_sidebar()
    {
        $this->hotaru->displayTemplate('custom_sidebar'); // Overrides the current sidebar with a new one.
        return true;
    }
    
    
    /**
     * FUNCTION #5
     *
     * Function: check_sent_form
     * Purpose: Checks the response from the form and prepares a message
     * Notes: This is used for the example form.
     */
    public function checkSentForm()
    {
        if ($this->hotaru->cage->post->getAlpha('submit_example') == 'true') {
            
            // This checks to see if someone is submitting this form from an external site, 
            // which is something hackers do. This stops them.
            if (!$this->hotaru->csrf()) { 
                $this->hotaru->message = $this->hotaru->lang['error_csrf'];
                $this->hotaru->messageType = 'red';
                return false;
            };
            
            $answer = $this->hotaru->cage->post->getMixedString2('answer');
            if ($answer && $answer == 'Paris') {
                $this->hotaru->message = $this->hotaru->lang['hello_universe_success'];
                $this->hotaru->messageType = 'green';
                return true;
            } else {
                $this->hotaru->message = $this->hotaru->lang['hello_universe_failure'];
                $this->hotaru->messageType = 'red';    
            }
        } 
        
        return false;
    }

}
?>