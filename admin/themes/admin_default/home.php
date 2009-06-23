<?php 

/* ******* ADMIN TEMPLATE *********
Theme name: admin_default
Template name: home.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************

***************************** */

global $hotaru; // don't remove
?>

<h2><a href="<?php echo baseurl ?>admin/admin_index.php">Hotaru Admin Control Panel</a> &raquo; Admin Home</h2>

<div class="admin_header admin_header_space"><a href="http://feeds2.feedburner.com/hotarucms"><img src="<?php echo baseurl ?>admin/themes/admin_default/images/rss_16.gif"></a>&nbsp; Latest from Hotaru CMS</div>
<?php echo admin_news(); ?>
