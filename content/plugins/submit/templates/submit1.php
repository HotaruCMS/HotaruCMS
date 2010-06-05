<?php
/**
 * Template for Submit: Submit Step 1
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
 
$submitted_url = urldecode($h->vars['submitted_data']['submit_orig_url']);
?>
<div id="submit_1">

    <?php echo $h->showMessage(); ?>
            

    <?php echo $h->lang["submit_instructions_1"]; ?>
    
    <form name='submit_1' action='<?php echo BASEURL; ?>index.php?page=submit1' method='post'>
        <?php echo $h->lang["submit_url"]; ?>&nbsp; 
        <input id='submit_orig_url'type='text' name='submit_orig_url' value='<?php echo $submitted_url; ?>' />
        <?php if ($h->currentUser->getPermission('can_post_without_link') == 'yes') { ?>
            <br /><input id='submit_no_link' type='checkbox' name='no_link'>&nbsp;<?php echo $h->lang["submit_post_without_link"]; ?>
        <?php } ?>

        <input type='hidden' name='submit1' value='true' />
        <input type='hidden' name='page' value='<?php echo $h->pageName; ?>' />
        <br />
        <input id="submit_button_1" type='submit' class='submit' name='submit' value='<?php echo $h->lang['main_form_next']; ?>' />
    </form>

</div>