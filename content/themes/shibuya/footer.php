<?php 
/**
 * Theme name: shibuya
 * Template name: footer.php
 * Template author: shibuya246
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
 * @author    shibuya246 <blog@shibuya246.com>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.shibuya246.com/
 */

?>

    <div id="ft">
        <div class="hotaru_logo">
        <?php            
        
            // Link to forums...
            echo "<p><a href='http://hotarucms.org' title='" . $h->lang["main_theme_footer_hotaru_link"] . "'><img src='" . BASEURL . "content/themes/" . THEME . "images/hotarucms.png' ";
            echo "alt='" . $h->lang["main_theme_footer_hotaru_link"] . "' /></a></p>";

            echo "<div class='theme_strapline'>Shibuya Theme by <a href='http://shibuya246.com'>shibuya246</a> created for hotarucms</div>";
        
            $h->showQueriesAndTime();

        ?>
        </div>
        <div class="W3C">
            <p>
                <a href="http://validator.w3.org/check?uri=referer"><img
                    src="http://www.w3.org/Icons/valid-xhtml10"
                    alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
              </p>
        </div>
        
    </div> <!-- close "ft" -->

    <?php $h->pluginHook('footer'); ?>

</div> <!-- close "yui-t7 first" -->

<?php $h->pluginHook('pre_close_body'); ?>

</body>
</html>
