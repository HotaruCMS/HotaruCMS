<?php 
/**
 * Theme name: admin_default
 * Template name: footer.php
 * Template author: Nick Ramsay
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

global $hotaru, $plugins, $lang; // don't remove
?>
    <div id="ft" role="contentinfo">
        <?php
            $plugins->checkActions('footer_top');
            $plugins->checkActions('footer');
            $plugins->checkActions('admin_footer');
            $hotaru->showQueriesAndTime();
            
            // Link to forums...
            echo "<p>" . $lang["admin_theme_footer_having_trouble_vist_forums"];
            echo " <a href='http://hotarucms.org'>HotaruCMS.org</a> ";
            echo $lang["admin_theme_footer_for_help"] . "</p>";
            
            $plugins->checkActions('footer_bottom'); 
        ?>
    </div>
</div>
</body>
</html>