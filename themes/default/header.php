<?php
/* ******* DEFAULT TEMPLATE *********
Theme name: default
Template name: header.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************

***************************** */

global $hotaru, $plugin; // don't remove
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <title><?php echo sitename; ?></title>
   <link rel="stylesheet" href="<?php echo baseurl . 'includes/YUI-CSS/reset-fonts-grids.css'; ?>" type="text/css">
   <link rel="stylesheet" href="<?php echo baseurl . 'themes/' . current_theme . 'style.css'; ?>" type="text/css">
   <link rel="shortcut icon" href="<?php echo baseurl; ?>favicon.ico">
</head>
<body>
<div id="doc2" class="yui-t7">
	<div id="hd" role="banner">
		<a href="<?php echo baseurl; ?>index.php"><img src="<?php echo baseurl; ?>themes/default/images/hotaru_468x60.png"></a><br />
		<!-- NAVIGATION -->
		<?php echo $hotaru->display_template('navigation'); ?>
	</div>
