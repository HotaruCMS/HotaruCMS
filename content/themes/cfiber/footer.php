<?php 
/**
 * Theme name: CFiber
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
<div class="clear">&nbsp;</div>	
	<div id="ft">
		<?php 
			$h->pluginHook('footer');
			
			// Link to forums...
			echo "<p>";
			echo "<a name='search' href='http://www.tr3ndy.com' title='Tr3ndy - CFiber Theme Hotaru'><img class='tr3ndy' src='" . BASEURL . "content/themes/" . THEME . "images/tr3ndy.png' ";
			echo "alt='Tr3ndy.com Web design' /></a><a href='http://hotarucms.org' title='" . $h->lang["main_theme_footer_hotaru_link"] . "'><img class='firefly' src='" . BASEURL . "content/themes/" . THEME . "images/hotaru-logo.png' ";
			echo "alt='" . $h->lang["main_theme_footer_hotaru_link"] . "' /></a>";
			echo "<a href='http://www.hotarucms.com' title='Follow us on Twitter'><img class='twitter' src='" . BASEURL . "content/themes/" . THEME . "images/twitter.png' alt='Follow us on Twitter' /></a>";			
			echo "<a href='http://www.hotarucms.com' title='Friend us on Facebook'><img class='facebook' src='" . BASEURL . "content/themes/" . THEME . "images/facebook.png' alt='Friend us on Facebook' /></a></p> ";			
			$h->showQueriesAndTime();
		?>

			<form name="search_form" action="<?php echo BASEURL; ?>index.php?page=search" method="get" >
				<input class="search_box" type="text" name="search" value="<?php echo $h->lang['search_text']; ?>" title="Start typing and hit ENTER" onfocus="if (this.value == '<?php echo $h->lang['search_text']; ?>') {this.value = '';}" />
				<input class="lens" alt="Search" type="image" name="searchsubmit" title="Search" src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/search.png" />
			</form>

	</div> <!-- close "ft" -->


<?php $h->pluginHook('pre_close_body'); ?>
</div> <!-- close "all" -->
</body>
</html>
