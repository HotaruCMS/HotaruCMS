<?php 

/* ******* DEFAULT TEMPLATE *********
Theme name: default
Template name: home.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************

***************************** */

global $hotaru; // don't remove
?>

<?php echo $hotaru->display_stories(10, 'latest'); ?>
