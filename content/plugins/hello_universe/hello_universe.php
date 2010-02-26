<?php
/**
 * name: Hello Universe
 * description: Demonstrates how to make plugins
 * version: 0.7
 * folder: hello_universe
 * class: HelloUniverse
 * hooks: theme_index_top, theme_index_main, theme_index_sidebar, profile_navigation
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

class HelloUniverse
{
    /**
     * FUNCTION #1
     *
     * Function: theme_index_top
     * Purpose: Set the page title on the default page (which is otherwise "page not found")
     * Notes: Uses theme_index_top in the hooks list at the top of this file.
    */
    public function theme_index_top($h)
    {
        if (!$h->pageName) { 
            $h->pageTitle = $h->lang["hello_universe"];
        }
    }
    
    
    /**
     * FUNCTION #2
     *
     * Function: theme_index_main
     * Purpose: Plugin welcome page with various options.
     * Notes: Uses theme_index_main in the hooks list at the top of this file.
    */
    public function theme_index_main($h)
    {
        switch ($h->pageName) {
            case 'plugin_template':
                $h->displayTemplate('plugin_template'); // Displays the page from this plugin folder
                return true;
                break;
            case 'form_example':
                $this->checkSentForm($h);    // Function #5
                $h->displayTemplate('form_example'); // Displays the page from this plugin folder
                return true;
                break;
            case 'profile_example':
                $user = $h->cage->get->testUsername('user');
                if ($user) {
                    // create a user object and fill it with user info
                    $h->vars['user'] = new UserAuth();
                    $user_info = $h->vars['user']->getUserBasic($h, 0, $user);
                    if ($user_info) {
                        // only show the page if the user exists:
                        $h->pageType = 'user';
                        $h->displayTemplate('users_navigation', 'users'); // Displays user navigation from Users plugin
                        $h->displayTemplate('profile_example'); // Displays the page from this plugin folder
                        return true;
                    }
                }
                break;
            case 'index':
                $this->mainPage($h);
                return true;
                break;
            default:
                // do nothing
                break;
        }
    }
    
    
    /**
     * FUNCTION #3
     *
     * Function: mainPage
     * Purpose: Output text for the main page.
     * Notes: No hooks. Called by Function #1
     */
    public function mainPage($h)
    {
        // Display output
        echo "<div id='hello_universe' style='margin: 1.0em; background-color: #eee;'>";
        echo "<h2>" . $h->lang["hello_universe"] . "</h2>";
        echo "<p>" . $h->lang["hello_universe_explanation"] . "</p>";  
        echo "<ul>";
        // Note these links allow for either friendly or unfriendly urls...
        echo "<li><a href='" . $h->url(array('page'=>'plugin_template')) . "'>" . $h->lang["hello_universe_see_page"] . "</a></li>";
        echo "<li><a href='" . $h->url(array('page'=>'form_example')) . "'>" . $h->lang["hello_universe_see_form"] . "</a></li>";
        echo "</ul></div>";
    }
    
    /**
     * FUNCTION #4
     *
     * Function: theme_index_sidebar
     * Purpose: A sidebar that overrides the real sidebar!
     * Notes: Uses theme_index_sidebar in the hooks list at the top of this file.
     */
    public function theme_index_sidebar($h)
    {
        $h->displayTemplate('custom_sidebar'); // Overrides the current sidebar with a new one.
        return true;
    }
    
    
    /**
     * FUNCTION #5
     *
     * Function: check_sent_form
     * Purpose: Checks the response from the form and prepares a message
     * Notes: This is used for the example form.
     */
    public function checkSentForm($h)
    {
        if ($h->cage->post->getAlpha('submit_example') == 'true') {
            
            // This checks to see if someone is submitting this form from an external site, 
            // which is something hackers do. This stops them.
            if (!$h->csrf()) { 
                $h->message = $h->lang['error_csrf'];
                $h->messageType = 'red';
                return false;
            };
            
            $answer = $h->cage->post->sanitizeTags('answer');
            if ($answer && $answer == 'Paris') {
                $h->message = $h->lang['hello_universe_success'];
                $h->messageType = 'green';
                return true;
            } else {
                $h->message = $h->lang['hello_universe_failure'];
                $h->messageType = 'red';    
            }
        } 
        
        return false;
    }
    
    
    /**
     * FUNCTION #6
     *
     * Profile navigation link
     */
    public function profile_navigation($h)
    {
        echo "<li><a href='" . $h->url(array('page'=>'profile_example', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang['hello_universe_profile_example'] . "</a></li>\n";
    }

}
?>
