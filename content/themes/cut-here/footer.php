<?php 
/**
 * Theme name: Cut Here
 * Template name: footer.php
 * Original Template author: Nick Ramsay
 * Original Design: Carlo Armanni
 * Original Author URI: www.tr3ndy.com
 * Template Author: Carlo Armanni
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
 * @author    Carlo Armanni <admin@tr3ndy.com>
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.tr3ndy.com/
 */

?>
	<div id="grigious" class="grid_12 alpha omega">
		<div id="scissors" class="grid_2 alpha">&nbsp;</div>	
		<div id="name" class="grid_5">
			<a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a>
		</div>
		<div id="slogan" class="grid_5">PUT YOUR SLOGAN HERE</div>
	</div>

    <div id="ft">
			<div id="ftprebox" class="grid_2">
				<strong>Be Social with Us</strong><br />
				Follow us on Twitter<br />
				Friend us on Facebook<br />
				Look at us at Flickr<br />
				Likn with us at <br />				
				Read us at Blog<br />
			</div>
			<div id="ftbox1" class="grid_2">
				<strong>Info</strong><br />
				About Us<br />
				FAQ<br />
				Terms<br />
				Privacy<br />
				Advertise<br />
			</div>
			<div id="ftbox2" class="grid_2">
				<strong>Best Friends</strong><br />
				Site Link1<br />
				Site Link2<br />
				Site Link3<br />
				Site Link4<br />
				Site Link5<br />
			</div>	
			<div id="ftbox3" class="grid_2">
				<strong>Sponsor</strong><br />
				Sponsor Site Link1<br />
				Sponsor Site Link2<br />
				Sponsor Site Link3<br />
				Sponsor Site Link4<br />
				Sponsor Site Link5<br />
			</div>			
			<div id="ftbox4end" class="grid_4 alpha">
				<strong>Contact Us</strong><br />
				Email: info@site.com<br />
				Telephone: +39.000.0000000<br />
				Cell. +39.000.000000<br />
				Live support<br />
				<span class="credits"><?php echo "<a href='http://hotarucms.org' title='" . $h->lang["main_theme_footer_hotaru_link"] . "'><img src='" . BASEURL . "content/themes/" . THEME . "images/hotarucms.png' style='vertical-align:text-bottom'";
				echo "alt='" . $h->lang["main_theme_footer_hotaru_link"] . "' /></a> // <small>Theme design by <a href='http://www.tr3ndy.com' title='Tr3ndy'>Tr3ndy</a></small>"; ?>
				</span>
	</div>
        <?php 
            $h->pluginHook('footer');
            $h->showQueriesAndTime();
        ?>
    </div> <!-- close "ft" -->
</div> <!-- close "" -->

<?php $h->pluginHook('pre_close_body'); ?>

</body>
</html>
