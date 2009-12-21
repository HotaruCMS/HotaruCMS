<?php
/**
 * Template for SB_Submit: Edit Post
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

$hotaru->pluginHook('submit_2_assign');

?>

    <?php echo $hotaru->showMessages(); ?>
    
    <?php echo $hotaru->lang["submit_edit_instructions"]; ?>

    <form name='submit_edit_post' action='<?php BASEURL; ?>index.php?page=edit_post' method='post'>
    <table>
    <tr>
        <td><?php echo $hotaru->lang["submit_url"]; ?>&nbsp; </td>
        <td><?php echo "<a target='_blank' href='" . $hotaru->vars['submit_orig_url'] . "'>" . $hotaru->vars['submit_orig_url'] . "</a>"; ?></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><?php echo $hotaru->lang["submit_title"]; ?>&nbsp; </td>
        <td><input type='text' id='post_title' name='post_title' value='<?php echo $hotaru->vars['submit_title']; ?>'></td>
        <td>&nbsp;</td>
    </tr>
    
    <?php if ($hotaru->vars['submit_use_content']) { ?>
    
    <tr>
        <td style='vertical-align: top;'><?php echo $hotaru->lang["submit_content"]; ?>&nbsp; </td>
        <td colspan=2>
            <textarea id='post_content' name='post_content' rows='6' maxlength='<?php echo $hotaru->vars["submit_content_length"]; ?>'><?php echo $hotaru->vars['submit_content']; ?></textarea>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td colspan=2 style='vertical-align: top;' class="submit_instructions"><?php echo $hotaru->lang['submit_allowable_tags']; ?>
            <?php echo $hotaru->vars['submit_allowable_tags']; ?>
        </td>
    </tr>
    <?php } ?>
    
    <?php $hotaru->pluginHook('submit_2_fields'); ?>
        
    <?php if ($hotaru->currentUser->getPermission('can_edit_posts') == 'yes') { ?>
        <!-- Admin/Mod only options -->
        
        <tr><td colspan=3><u><?php echo $hotaru->lang["submit_edit_admin_only"]; ?></u></td></tr>
        
        <?php if (!$hotaru->vars['submit_editorial']) { // if not editorial, allow source url to be changed: ?>
            <tr>
                <td><?php echo $hotaru->lang["submit_url"]; ?>&nbsp; </td>
                <td><input type='text' id='post_orig_url' name='post_orig_url' value='<?php echo $hotaru->vars['submit_orig_url']; ?>'></td>
                <td>&nbsp;</td>
            </tr>
        <?php } ?>
        
        <tr>
            <td style='vertical-align: top;'><?php echo $hotaru->lang["submit_edit_status"]; ?>&nbsp; </td>
            <td><select name='post_status'>
                <option value="<?php echo $hotaru->vars['submit_status']; ?>"><?php echo $hotaru->vars['submit_status']; ?></option>
                <?php echo $hotaru->vars['submit_status_options']; ?>
            </td>
        </tr>
        
        <?php $hotaru->pluginHook('submit_edit_admin_fields'); ?>
        <!-- END Admin only options -->
    <?php } ?>
        
    <?php if (($hotaru->currentUser->getPermission('can_edit_posts') != 'yes') || $hotaru->vars['submit_editorial']) { 
        // use this hidden input to send back the original url when the above form is not used ?>
        <input type='hidden' name='post_orig_url' value='<?php echo $hotaru->vars['submit_orig_url']; ?>' />
        <?php $hotaru->pluginHook('submit_edit_non_admin_hidden_fields'); ?>
    <?php } ?>
    
    <input type='hidden' name='from' value='<?php echo $hotaru->vars['submit_pm_from']; ?>' />
    <input type='hidden' name='search_value' value='<?php echo $hotaru->vars['submit_pm_search']; ?>' />
    <input type='hidden' name='post_status_filter' value='<?php echo $hotaru->vars['submit_pm_filter']; ?>' />
    <input type='hidden' name='pg' value='<?php echo $hotaru->vars['submit_pm_page']; ?>' />
    <input type='hidden' name='submit_post_id' value='<?php echo $hotaru->post->id; ?>' />
    <input type='hidden' name='edit_post' value='true' />
    <input type='hidden' name='csrf' value='<?php echo $hotaru->csrfToken; ?>' />
    
    <tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' class='submit' name='submit_edit_post' value='<?php echo $hotaru->lang["main_form_update"]; ?>' /></td></tr>    
    </table>
    </form>
    
    <?php if ($hotaru->currentUser->getPermission('can_delete_posts') == 'yes') { ?>
        <a class='bold_red' href="<?php echo $hotaru->url(array('page'=>'edit_post', 'post_id'=>$hotaru->post->id, 'action'=>'delete')); ?>">
        <?php echo $hotaru->lang["submit_edit_delete"]; } ?>
        </a>
