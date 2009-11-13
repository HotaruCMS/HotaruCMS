<?php
/**
 * Template for Submit: Submit Step 3
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>
    <div id="breadcrumbs"><a href='<?php echo BASEURL; ?>'><?php echo $hotaru->lang['submit_form_home']; ?></a> &raquo; <?php echo $hotaru->lang["submit_form_step3"]; ?></div>
        
    <?php echo $hotaru->lang["submit_form_instructions_3"]; ?> <br /><br />
    
    <?php $hotaru->displayTemplate('post', 'submit') ?>
    
    <?php $hotaru->plugins->pluginHook('submit_step3_pre_buttons'); ?>
    
    <div id="submit_edit_confirm">
    
        <!-- EDIT BUTTON -->
        <form name='submit_form_3' action='<?php BASEURL; ?>index.php?page=submit3' method='post'>
        <input type='hidden' name='post_id' value='<?php echo $hotaru->post->id; ?>' />
        <input type='hidden' name='submit3' value='edit' />
        <input type='submit' name='submit' onclick="javascript:safeExit=true;" class='submit' value='<?php echo $hotaru->lang['submit_form_submit_edit_button']; ?>' />
        </form>    

        <!-- CONFIRM BUTTON -->
        <form name='submit_form_3' action='<?php BASEURL; ?>index.php?page=submit3' method='post'>
        <input type='hidden' name='post_id' value='<?php echo $hotaru->post->id; ?>' />
        <input type='hidden' name='submit3' value='confirm' />
        <input type='submit' name='submit' onclick="javascript:safeExit=true;" class='submit' value='<?php echo $hotaru->lang['submit_form_submit_confirm_button']; ?>' />
        </form>
    </div>
    
    <?php $hotaru->plugins->pluginHook('submit_step3_post_buttons'); ?>
    