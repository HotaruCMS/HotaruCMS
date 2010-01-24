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
<div id="submit_3">

    <?php echo $h->lang["submit_instructions_3"]; ?> <br /><br />
    
    <?php $h->displayTemplate('sb_post', 'sb_base') ?>
    
    <?php $h->pluginHook('submit_step3_pre_buttons'); ?>
    
    <div id="submit_edit_confirm">
    
        <!-- EDIT BUTTON -->
        <form name='submit_3' action='<?php echo BASEURL; ?>index.php?page=submit3' method='post'>
        <input type='hidden' name='submit_post_id' value='<?php echo $h->post->id; ?>' />
        <input type='hidden' name='submit3edit' value='true' />
        <input type='hidden' name='submit_key' value='<?php echo $h->vars['submit_key']; ?>' />
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
        <input type='submit' name='submit' onclick="javascript:safeExit=true;" class='submit' value='<?php echo $h->lang['main_form_edit']; ?>' />
        </form>    

        <!-- CONFIRM BUTTON -->
        <form name='submit_3' action='<?php echo BASEURL; ?>index.php?page=submit_confirm' method='post'>
        <input type='hidden' name='submit_post_id' value='<?php echo $h->post->id; ?>' />
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
        <input type='submit' name='submit' onclick="javascript:safeExit=true;" class='submit' value='<?php echo $h->lang['main_form_confirm']; ?>' />
        </form>
    </div>
    
    <?php $h->pluginHook('submit_step3_post_buttons'); ?>
    
</div>