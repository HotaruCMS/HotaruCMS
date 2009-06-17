<?php 

/* ******* ADMIN TEMPLATE *********
Theme name: default
Template name: header.php
Template author: Nick Ramsay
Version: 0.1
Last updated: June 15th 2009
***************************** */

/* ******* USAGE ************

***************************** */

global $hotaru; // don't remove
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <title><?php echo sitename; ?></title>
   <link rel="stylesheet" href="<?php echo baseurl . 'includes/YUI-CSS/reset-fonts-grids.css'; ?>" type="text/css">
   <link rel="stylesheet" href="<?php echo baseurl . 'admin/themes/' . current_admin_theme . 'style.css'; ?>" type="text/css">
   <script language="JavaScript" src="<?php echo baseurl . 'javascript/hotaru_ajax.js'; ?>"></script>
   <script language="JavaScript" src="<?php echo baseurl . 'javascript/jQuery/jquery.min.js'; ?>"></script>
   <script language="JavaScript" src="<?php echo baseurl . 'javascript/jQuery/jquery-ui.min.js'; ?>"></script>
   <script language="JavaScript" src="<?php echo baseurl . 'javascript/jQuery/jquery.easywidgets.min.js'; ?>"></script>
   <script language="JavaScript" src="<?php echo baseurl . 'javascript/jQuery/hotaru_jquery.js'; ?>"></script>
</head>
<body>