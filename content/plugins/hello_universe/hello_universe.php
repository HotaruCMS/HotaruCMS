<?php
/**
 * name: Hello Universe
 * description: Demonstrates how to make plugins
 * version: 0.4
 * folder: hello_universe
 * class: HelloUniverse
 * hooks: theme_index_main, theme_index_sidebar, hotaru_header
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


class HelloUniverse extends PluginFunctions
{
    /**
     * FUNCTION #1
     *
     * Function: theme_index_main
     * Purpose: Plugin welcome page with various options.
     * Notes: Uses theme_index_main in the hooks list at the top of this file.
    */
    public function theme_index_main()
    {
        // These lines get the current page and display any matches...
        $page = $this->hotaru->getPageName();
        switch($page) {
            case 'plugin_template':
                $this->hotaru->displayTemplate('plugin_template'); // Displays the page from this plugin folder
                return true;
                break;
            case 'form_example':
                $this->check_sent_form();    // Function #5
                $this->hotaru->displayTemplate('form_example'); // Displays the page from this plugin folder
                return true;
                break;
            case 'main':
                $this->main_page();
                return true;
                break;
            default:
                break;
        }
    }
    
    
    /**
     * FUNCTION #2
     *
     * Function: main_page
     * Purpose: Output text for the main page.
     * Notes: No hooks. Called by Function #1
     * IMPORTANT:     Since we're echo'ing text to the main page, there's a very good chance it will appear 
     *       above or below content from *another* plugin using the same page. The way around that, 
     *        which we're not doing here, is to create another template along with plugin_template.php 
     *       and form_example.php and echo the text from there. Any other pages we make could use the 
     *       same template instead of making new ones for every page.
     */
    public function main_page()
    {
        // If the current page is "main" (which it is by default)...
        echo "<div id='hello_universe' style='margin: 1.0em; background-color: #eee;'>";
        echo "<h2>" . $this->lang["hello_universe"] . "</h2>";
        echo "<p>" . $this->lang["hello_universe_explanation"] . "</p>";  
        echo "<ul>";
        // Note these links allow for either friendly or unfriendly urls...
        echo "<li><a href='" . $this->hotaru->url(array('page'=>'plugin_template')) . "'>" . $this->lang["hello_universe_see_page"] . "</a></li>";
        echo "<li><a href='" . $this->hotaru->url(array('page'=>'form_example')) . "'>" . $this->lang["hello_universe_see_form"] . "</a></li>";
        echo "</ul></div>";
    }
    
    /**
     * FUNCTION #3
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
     * FUNCTION #4
     *
     * Function: hotaru_header
     * Purpose: Includes the Hello Universe language file
     * Notes: This is used in the example form.
     */
    public function hotaru_header()
    {
        // include hello_universe language file
        $this->includeLanguage();
    
    }
    
    
    /**
     * FUNCTION #5
     *
     * Function: check_sent_form
     * Purpose: Checks the response from the form and prepares a message
     * Notes: This is used for the example form.
     */
    public function check_sent_form()
    {
        if ($this->cage->post->getAlpha('submit_example') == 'true') {
            $answer = $this->cage->post->getMixedString2('answer');
            if ($answer && $answer == 'Paris') {
                $this->hotaru->message = $this->lang['hello_universe_success'];
                $this->hotaru->messageType = 'green';
                return true;
            } else {
                $this->hotaru->message = $this->lang['hello_universe_failure'];
                $this->hotaru->messageType = 'red';    
            }
        } 
        
        return false;
    }

}
?>