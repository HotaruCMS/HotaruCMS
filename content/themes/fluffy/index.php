<?php 
/**
 * Theme name: default
 * Template name: index.php
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

// plugin hook
$result = $h->pluginHook('theme_index_top');
if (!$result) {
?>
        <!-- HEADER-->
        <?php
            // plugin hook
            $result = $h->pluginHook('theme_index_header');
            if (!$result) {
                $h->displayTemplate('header');
            }
        ?>
		<div id="prebd" class="grid_12 alpha omega">
			<div id="social">&nbsp; 
				<a href="http://www.hotarucms.org" title="Follow us on Twitter"><img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/twitter.png" alt="Follow us on Twitter" /></a> 
				<a href="http://www.hotarucms.org" title="Friend us on Facebook"><img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/facebook.png" alt="Friend us on Facebook" /></a> 
				<a href="http://www.hotarucms.org" title="See us on Flickr"><img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/flickr.png" alt="See us on Flickr" /></a> 
				<a href="http://www.hotarucms.org" title="Stumble us on StumbleUpon"><img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/stumbleupon.png" alt="Stumble us on StumbleUpon" /></a> 
				<img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/blank.png" alt="Blankspace" /> 
				<a href="http://www.hotarucms.org" title="About Us"><img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/info.png" alt="About Us" /></a> 
				<a href="http://www.hotarucms.org" title="FAQ"><img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/faq.png" alt="FAQ" /></a> 
				<a href="http://www.hotarucms.org" title="Privacy"><img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/privacy.png" alt="Privacy" /></a> 
				<a href="http://www.hotarucms.org" title="Advertising"><img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/advertising.png" alt="Advertising" /></a> 
				<a href="http://www.hotarucms.org" title="Contact Us"><img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/mail.png" alt="Contact Us" /></a> 
				<a href="<?php echo $h->url(array('page'=>'rss')); ?>" title="RSS"><img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/rss.png" alt="RSS" /></a> 
			</div>
					<div id="search">
					<form name="search_form" action="<?php echo BASEURL; ?>index.php?page=search" method="get" >
					<input class="searchbox" type="text" name="search" value="<?php echo $h->lang['search_text']; ?>" title="Start typing and hit ENTER" onfocus="if (this.value == '<?php echo $h->lang['search_text']; ?>') {this.value = '';}" />
					<input class="lens" alt="Search" type="image" name="searchsubmit" title="Search" src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/search.png" />
					</form>
				</div>
		</div> <!-- close "prebd" -->
        
		<div id="bd" class="grid_12">
            <?php if ($h->sidebars) { // determines whether to show the sidebar or not ?>
                    <div id="news" class="grid_8 alpha omega">
						<div id="news_c">
                <?php } ?>
							<div id="header_end" class="grid_7 alpha">
							<!-- CATEGORIES, ETC -->
							<?php $h->pluginHook('header_end'); ?>
							</div>
							<div class="clear"></div>
                            <!-- BREADCRUMBS -->
                            <div id='breadcrumbs'>
                                <?php echo $h->breadcrumbs(); ?>
                            </div>
                            
                            <!-- POST BREADCRUMBS -->
                            <?php 
                                // plugin hook
                                $result = $h->pluginHook('theme_index_post_breadcrumbs');
                            ?>
                            
                            <!-- FILTER TABS -->
                            <?php 
                                // plugin hook
                                $result = $h->pluginHook('theme_index_pre_main');
                            ?>
                            
                            <!-- MAIN -->
                            <?php     
                                // plugin hook
                            $result = $h->pluginHook('theme_index_main');
                            if (!$result) {
                                $h->displayTemplate($h->pageName); 
                            }
                            ?>
						</div> <!-- close "news_c" -->
					</div> <!-- close "news" -->
                    <?php if ($h->sidebars) { ?>

                            
                            <!-- SIDEBAR -->
                            <?php
                                // plugin hook
                                $result = $h->pluginHook('theme_index_sidebar');
                                if (!$result) {
                                    $h->displayTemplate('sidebar');
                                }                                
                            ?>
                    <?php } ?>

        </div> <!-- close "bd" -->
        <!-- FOOTER -->
        <?php
            // plugin hook
            $result = $h->pluginHook('theme_index_footer');
            if (!$result) {
                $h->displayTemplate('footer');
            }
        ?>
<?php    } ?>
