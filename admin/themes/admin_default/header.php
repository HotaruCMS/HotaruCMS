<?php 

/* ******* ADMIN TEMPLATE *********
Theme name: admin_default
Template name: header.php
Template author: Nick Ramsay
Version: 0.1
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
   <script>
	var baseurl = '<?php echo baseurl; ?>';
	
	$(document).ready(function(){
		$(function(){
		
		  // Prepare the Easy Widgets for Plugin Management
		
		  $.fn.EasyWidgets({
		  
		  callbacks : {
		      
			onChangePositions : function(str){
				widget_moved(baseurl,str);
			}
		      
		    }
		
		  });
		
		});
	});
   </script>
</head>
<body>
<div id="doc2" class="yui-t7">
	<div id="hd" role="banner">
		<a href="<?php echo baseurl; ?>index.php"><img src="<?php echo baseurl; ?>admin/themes/admin_default/images/hotaru_468x60.png"></a><br />
		<!-- NAVIGATION -->
		<?php echo $hotaru->display_admin_template('navigation'); ?>
	</div>