<?php 
/**
 * Theme name: admin_default
 * Template name: plugin_settings.php
 * Template author: shibuya246
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
 * @link      http://www.hotarucms.org/
 */

?>
<ul class="nav nav-pills">
  <li class="active"><a href="#">Home</a></li>
  <li><a href="?page=media&folder=post_images">Post Images</a></li>
  <li><a href="?page=media&folder=profile_images">Users</a></li>
</ul>
<hr/>

<div class="row">
<div class="superbox col-sm-12">
<?php 
    $folder = (!isset($h->vars['media_folder']) || $h->vars['media_folder'] == null) ? 'post_images/' : $h->vars['media_folder'] . '/';
    if (isset($h->vars['media_folder']) && $h->vars['media_folder'] == 'profile_images') { $folder = 'profile_images/'; }
    $siteFolder = CONTENT . 'images/' . $folder;
    $siteUrl = SITEURL . 'content/images/' . $folder;
    
    $files = $h->getFiles($siteFolder);
    $x=0;
    if ($files) {
        foreach ($files as $file) {
            $x++;
            ?>

    <div class="superbox-list">
            <img src="<?php echo $siteUrl . $file; ?>" data-img="<?php echo $siteUrl . $file; ?>" title="image1" alt="abc" class="superbox-img">
    </div>
    <?php
        }
    }
    ?>
</div>
</div>

<script type="">

$(document).ready(function(){
    $(function() {
      $('.superbox').SuperBox();
    });
});

</script>
