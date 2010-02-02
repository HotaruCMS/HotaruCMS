<?php
/**
 * File: plugins/jquery_test/jquery_test_settings.php
 * Purpose: The functions that do the hard work such as adding, deleting and sorting categories.
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
 * @author    Shibuya246 <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
    
class JQueryTestSettings
{
    /**
     * Main function that calls others
     *
     * @return bool
     */
    public function settings($h)
    {    
        // get the current saved settings:
        //$sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_status = %s";
        //$num_pending = $h->db->get_var($h->db->prepare($sql, 'pending'));
        //if (!$num_pending) { $num_pending = "0"; } 
        //h->vars['num_pending'] = $num_pending; 
        
        // clear variables:
        //$h->vars['search_term'] = '';
        //$h->vars['comment_status_filter'] = 'all';
        
        
        //save new settings
		        
        // show header
        echo "<h1>" . $h->lang["jquery_test_settings_header"] . "</h1>\n";

		 // instructions
        echo "<p>" . $h->lang["jquery_test_settings_instructions"] . "</p>\n";

		// box		
		echo '<div id="jquery_test_container">';
			echo '<p><a href="#" class="run">' . $h->lang["jquery_test_settings_start"] . '</a></p>';
			echo '<div id="jquery-test-box">';
			echo '</div>';
		echo '</div>';
		echo '<p id = "jquery-test-return">' . $h->lang["jquery_test_settings_result"] . '</p>';
         
	}
}
?>