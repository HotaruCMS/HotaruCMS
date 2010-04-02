<?php
/**
 * Template for Link Bar
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
 * @link      http://www.hotarucms.org/
 */
?>

<?php $h->pluginHook('link_bar_top'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <title><?php echo $h->getTitle(); ?></title>

        <?php
            // plugin hook
            $result = $h->pluginHook('header_meta');
            if (!$result) { ?>
                <meta name="description" content="<?php echo $h->lang['header_meta_description']; ?>" />
                <meta name="keywords" content="<?php echo $h->lang['header_meta_keywords']; ?>" />
        <?php } ?>

    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js?ver=1.4.2'></script>
    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js?ver=1.8.0'></script>
    
    <?php $h->pluginHook('link_bar_css_js'); ?>

    <?php if (file_exists(THEMES . THEME . 'css/link_bar.css')) { ?>
        <link rel="stylesheet" href="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>css/link_bar.css" type="text/css" />
    <?php } else { ?>
        <link rel="stylesheet" href="<?php echo BASEURL; ?>content/plugins/link_bar/css/link_bar.css" type="text/css" />
    <?php } ?>

</head>
<body>
<?php // plugin hook - can override the whole link bar body
    $result = $h->pluginHook('link_bar');
    if (!$result) { ?>
        <div id="link_bar">
    
            <div class="link_bar_logo">
                <?php // plugin hook
                    $result = $h->pluginHook('link_bar_logo');
                    if (!$result) { ?>
                        <a id="link_bar_logo_link" href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a>
                <?php } ?>
            </div>
    
            <div class="link_bar_post">
                <?php // plugin hook
                    $result = $h->pluginHook('link_bar_post');
                    if (!$result) { ?>
                        <a id="link_bar_hotaru_post" href="<?php echo $h->url(array('page'=>$h->post->id)); ?>">View this post on <?php echo SITE_NAME; ?></a>
                <?php } ?>
            </div>
        
            <div class="link_bar_more">
                <?php // plugin hook
                    $result = $h->pluginHook('link_bar_more');
                    if (!$result) { ?>
                        <a id="link_bar_orig_url" href="<?php echo $h->post->origUrl ?>">Close</a>
                <?php } ?>
            </div>
        </div>
<?php } ?>

<?php $h->pluginHook('link_bar_end'); ?>
</body>
</html>

<iframe src="<?php echo $h->post->origUrl ?>" id="source" frameborder="0" scrolling="auto"></iframe>

