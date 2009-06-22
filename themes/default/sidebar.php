<?php 

/* ******* DEFAULT TEMPLATE *********
Theme name: default
Template name: sidebar.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************

***************************** */

global $plugin; // don't remove
?>
<ul id="sidebar">	
	<?php $plugin->check_actions('hello_world'); ?>
	<?php $plugin->check_actions('rss_sidebar'); ?>
</ul>