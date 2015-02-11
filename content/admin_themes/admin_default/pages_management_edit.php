<?php 
/**
 * Theme name: admin_default
 * Template name: pages_management_edit.php
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

$h->template('admin_sidebar');

$h->showMessages();

?>

<a href="/admin_index.php?page=pages_management">Back to pages list</a>

<div class="h3">
    Filename : <?php echo $h->vars['admin_edit_page'] . '.php'; ?>
</div>

<div class="">
<button id="edit" class="btn btn-primary" onclick="edit()" type="button">Edit</button>
<button id="save" class="btn btn-primary" onclick="save()" type="button">Save</button>
<br/><br/>
</div>

<div class="row">
<div class="col-md-9">
<div class="click2edit">
    
        <?php include CONTENT . 'pages/' . $h->vars['admin_edit_page'] . '.php'; ?>
    
</div>
</div>
<div class="col-md-3">
    sidebar
</div>
</div>

<script type="text/javascript">
    var edit = function() {
      $('.click2edit').summernote({focus: true});
    };
    var save = function() {
      var aHTML = $('.click2edit').code(); //save HTML If you need(aHTML: array).
      $('.click2edit').destroy();
    };
</script>