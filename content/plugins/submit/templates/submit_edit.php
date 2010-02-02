<?php
/**
 * Template for Submit: Edit Post
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

$h->pluginHook('submit_2_assign');

?>

<div id="submit_edit">

    <?php echo $h->showMessages(); ?>
    
    <?php echo $h->lang["submit_edit_instructions"]; ?>

    <form name='submit_edit_post' action='<?php echo BASEURL; ?>index.php?page=edit_post' method='post'>
    <table>
    <tr>
        <td><?php echo $h->lang["submit_url"]; ?>&nbsp; </td>
        <td><?php echo "<a target='_blank' href='" . $h->vars['submit_orig_url'] . "'>" . $h->vars['submit_orig_url'] . "</a>"; ?></td>
    </tr>
    <tr>
        <td><?php echo $h->lang["submit_title"]; ?>&nbsp; </td>
        <td><input type='text' id='post_title' name='post_title' value='<?php echo $h->vars['submit_title']; ?>'></td>
    </tr>
    
    <?php if ($h->vars['submit_use_content']) { ?>
    
    <tr>
        <td style='vertical-align: top;'><?php echo $h->lang["submit_content"]; ?>&nbsp; </td>
        <td>
            <textarea id='post_content' name='post_content' rows='6'><?php echo $h->vars['submit_content']; ?></textarea>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td style='vertical-align: top;' class="submit_instructions"><?php echo $h->lang['submit_allowable_tags']; ?>
            <?php echo $h->vars['submit_allowable_tags']; ?>
        </td>
    </tr>
    <?php } ?>
    
    <?php if ($h->vars['submit_use_categories']) { ?>
    <tr>
        <td style='vertical-align: top;'><?php echo $h->lang["submit_category"]; ?>&nbsp; </td>
        <td><select name='post_category'>
            <?php echo $h->vars['submit_category_picker']; ?>
        </select></td>
    </tr>
    <?php } ?>
    
    <?php
        if ($h->vars['submit_use_tags']) { 
            echo "<tr>";
                echo "<td>" . $h->lang["submit_tags"] . "&nbsp; </td>";
                echo "<td><input type='text' id='post_tags' name='post_tags' value='" . $h->vars['submit_tags'] . "'></td>";
            echo "</tr>";
        }
    ?>
    
    <?php $h->pluginHook('submit_2_fields'); ?>
        
    <?php if ($h->currentUser->getPermission('can_edit_posts') == 'yes') { ?>
        <!-- Admin/Mod only options -->
        
        <tr><td colspan=3><u><?php echo $h->lang["submit_edit_admin_only"]; ?></u></td></tr>
        
        <?php if (!$h->vars['submit_editorial']) { // if not editorial, allow source url to be changed: ?>
            <tr>
                <td><?php echo $h->lang["submit_url"]; ?>&nbsp; </td>
                <td><input type='text' id='post_orig_url' name='post_orig_url' value='<?php echo $h->vars['submit_orig_url']; ?>'></td>
            </tr>
        <?php } ?>
        
        <tr>
            <td style='vertical-align: top;'><?php echo $h->lang["submit_edit_status"]; ?>&nbsp; </td>
            <td><select name='post_status'>
                <option value="<?php echo $h->vars['submit_status']; ?>"><?php echo $h->vars['submit_status']; ?></option>
                <?php echo $h->vars['submit_status_options']; ?>
            </td>
        </tr>
        
        <?php $h->pluginHook('submit_edit_admin_fields'); ?>
        <!-- END Admin only options -->
    <?php } ?>
    
    <input type='hidden' name='from' value='<?php echo $h->vars['submit_pm_from']; ?>' />
    <input type='hidden' name='search_value' value='<?php echo $h->vars['submit_pm_search']; ?>' />
    <input type='hidden' name='post_status_filter' value='<?php echo $h->vars['submit_pm_filter']; ?>' />
    <input type='hidden' name='pg' value='<?php echo $h->vars['submit_pm_page']; ?>' />
    <input type='hidden' name='submit_post_id' value='<?php echo $h->post->id; ?>' />
    <input type='hidden' name='edit_post' value='true' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    
    <tr><td>&nbsp; </td><td style='text-align:right;'><input type='submit' class='submit' name='submit_edit_post' value='<?php echo $h->lang["main_form_update"]; ?>' /></td></tr>    
    </table>
    </form>
    
    <?php if ($h->currentUser->getPermission('can_delete_posts') == 'yes') { ?>
        <a class='bold_red' href="<?php echo $h->url(array('page'=>'edit_post', 'post_id'=>$h->post->id, 'action'=>'delete')); ?>">
        <?php echo $h->lang["submit_edit_delete"]; ?>
        </a>
    <?php } ?>
    
    <?php $h->pluginHook('submit_edit_end', '', array('userid'=>$h->post->author)); ?>
        
</div>