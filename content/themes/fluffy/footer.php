<?php 
/**
 * Theme name: default
 * Template name: footer.php
 * Template author: Carlo Armanni
 * Template author website: http://www.tr3ndy.com
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
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>
<div class="clear"></div>

</div> 

</div>
    <div id="ft">
        <?php 
            $h->pluginHook('footer');
        
            // Link to forums...
            echo "<p><a href='http://www.tr3ndy.com' title='Original Design by Tr3ndy.com'><img src='" . BASEURL . "content/themes/" . THEME . "images/tr3ndy.png' alt='Original Design by Tr3ndy.com' /></a> for <a href='http://hotarucms.org' title='" . $h->lang["main_theme_footer_hotaru_link"] . "'><img src='" . BASEURL . "content/themes/" . THEME . "images/hotarucms.png' ";
            echo "alt='" . $h->lang["main_theme_footer_hotaru_link"] . "' /></a></p>";
        
            $h->showQueriesAndTime();
        ?>
    </div> <!-- close "ft" -->
	<?php $h->pluginHook('pre_close_body'); ?>
</body>
</html>
