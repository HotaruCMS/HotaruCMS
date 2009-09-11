<?php
/**
 * name: Hello Universe
 * description: Demonstrates how to make plugins
 * version: 0.2
 * folder: hello_universe
 * prefix: hu
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


/**
 * FUNCTION #1
 *
 * Function: hw_theme_index_main
 * Purpose: Plugin welcome page with various options.
 * Notes: Uses theme_index_main in the hooks list at the top of this file.
*/
function hu_theme_index_main()
{
    global $hotaru;
    
    // These lines get the current page and display any matches...
    $page = $hotaru->get_page_name();
    switch($page) {
        case 'plugin_template':
            $hotaru->display_template('plugin_template', 'hello_universe'); // Displays the page from this plugin folder
            return true;
            break;
        case 'form_example':
            hu_check_sent_form();    // Function #5
            $hotaru->display_template('form_example', 'hello_universe'); // Displays the page from this plugin folder
            return true;
            break;
        case 'main':
            hu_main_page();
            return true;
            break;
        default:
            break;
    }
}


/**
 * FUNCTION #2
 *
 * Function: hu_main_page
 * Purpose: Output text for the main page.
 * Notes: No hooks. Called by Function #1
 * IMPORTANT:     Since we're echo'ing text to the main page, there's a very good chance it will appear 
 *       above or below content from *another* plugin using the same page. The way around that, 
 *        which we're not doing here, is to create another template along with plugin_template.php 
 *       and form_example.php and echo the text from there. Any other pages we make could use the 
 *       same template instead of making new ones for every page.
 */
function hu_main_page()
{
    global $lang;
    
    // If the current page is "main" (which it is by default)...
    echo "<div id='hello_universe' style='margin: 1.0em; background-color: #eee;'>";
    echo "<h2>" . $lang["hello_universe"] . "</h2>";
    echo "<p>" . $lang["hello_universe_explanation"] . "</p>";  
    echo "<ul>";
    // Note these links allow for either friendly or unfriendly urls...
    echo "<li><a href='" . url(array('page'=>'plugin_template')) . "'>" . $lang["hello_universe_see_page"] . "</a></li>";
    echo "<li><a href='" . url(array('page'=>'form_example')) . "'>" . $lang["hello_universe_see_form"] . "</a></li>";
    echo "</ul></div>";
}

/**
 * FUNCTION #3
 *
 * Function: hu_theme_index_sidebar
 * Purpose: A sidebar that overrides the real sidebar!
 * Notes: Uses theme_index_sidebar in the hooks list at the top of this file.
 */
function hu_theme_index_sidebar()
{
    global $hotaru;

    $hotaru->display_template('custom_sidebar', 'hello_universe'); // Overrides the current sidebar with a new one.
    return true;
}


/**
 * FUNCTION #4
 *
 * Function: hu_hotaru_header
 * Purpose: Includes the Hello Universe language file
 * Notes: This is used in the example form.
 */
function hu_hotaru_header()
{
    global $lang, $plugins;
    
    // include hello_universe language file
    $plugins->includeLanguage('hello_universe');
}


/**
 * FUNCTION #5
 *
 * Function: hu_check_sent_form
 * Purpose: Checks the response from the form and prepares a message
 * Notes: This is used for the example form.
 */
function hu_check_sent_form()
{
    global $hotaru, $cage, $lang;

    if ($cage->post->getAlpha('submit_example') == 'true') {
        $answer = $cage->post->getMixedString2('answer');
        if ($answer && $answer == 'Paris') {
            $hotaru->message = $lang['hello_universe_success'];
            $hotaru->message_type = 'green';
            return true;
        } else {
            $hotaru->message = $lang['hello_universe_failure'];
            $hotaru->message_type = 'red';    
        }
    } 
    
    return false;
}

?>