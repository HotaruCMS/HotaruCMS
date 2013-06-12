<?php
/**
 * Theme name: classic
 * Template name: header.php
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://hotarucms.org/
 */

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="generator" content="Hotaru CMS <?php echo $h->version; ?>" />

	<title><?php echo $h->getTitle(); ?></title>
	
		<?php
			// plugin hook
			$result = $h->pluginHook('header_meta');
			if (!$result) { ?>
				<meta name="description" content="<?php echo $h->lang('header_meta_description'); ?>" />
				<meta name="keywords" content="<?php echo $h->lang('header_meta_keywords'); ?>" />
		<?php } ?>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	
	<!-- include 960 CSS framework -->
	<?php $css = SITEURL . "content/themes/" . THEME . "css/"; ?>
	<link rel="stylesheet" href="<?php echo $css; ?>960/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $css; ?>960/960.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $css; ?>960/text.css" type="text/css" />
	
	<!-- Include merged files for all the plugin css and javascript (if any) -->
	<?php $h->doIncludes(); ?>
	<!-- End -->
	
	<link rel="stylesheet" href="<?php echo $css; ?>style.css" type="text/css" />
	<!-- <link rel="shortcut icon" href="<?php echo SITEURL; ?>favicon.ico" /> -->
	
	<?php $h->pluginHook('header_include_raw'); ?>
	
</head>