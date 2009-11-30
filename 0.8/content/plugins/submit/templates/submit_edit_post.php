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

if ($hotaru->cage->post->getAlpha('edit_post') == 'true') {
    // Submitted this form...
    $title_check = $hotaru->cage->post->noTags('post_title');    
    $content_check = sanitize($hotaru->cage->post->getHtmLawed('post_content'), 2, $hotaru->post->allowableTags);
    $content_check = stripslashes($content_check);
    if ($hotaru->cage->post->keyExists('post_subscribe')) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }   
    $status_check = $hotaru->cage->post->testAlnumLines('post_status');
    $post_orig_url = $hotaru->cage->post->testUri('post_orig_url');
    $post_id = $hotaru->cage->post->getInt('post_id');    
    $hotaru->post->id = $post_id;
    // from post manager...
    $from = $hotaru->cage->post->testAlnumLines('from');
    $search_value = $hotaru->cage->post->getMixedString2('search_value'); 
    $post_status_filter = $hotaru->cage->post->testAlnumLines('post_status_filter');
    $pg = $hotaru->cage->post->testInt('pg');
    
} elseif ($hotaru->cage->get->testInt('post_id'))  {
    if ($hotaru->cage->get->getAlpha('action') == 'delete') { $hotaru->showMessages(); return true; } // die on deletion
    $post_id = $hotaru->cage->get->testInt('post_id');
    $hotaru->post->readPost($post_id);
    $title_check = $hotaru->post->title;
    $content_check = $hotaru->post->content;
    if ($hotaru->post->subscribe == 1) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
    $status_check = $hotaru->post->status;
    $post_orig_url = $hotaru->post->origUrl;
    $post_id = $hotaru->post->id;
    // from post manager...
    $from = $hotaru->cage->get->testAlnumLines('from');
    $search_value = $hotaru->cage->get->getMixedString2('search_value'); 
    $post_status_filter = $hotaru->cage->get->testAlnumLines('post_status_filter');
    $pg = $hotaru->cage->get->testInt('pg');
}

$user = new UserBase($hotaru);
$user->getUserBasic($hotaru->post->author);

$can_edit = false;
if ($hotaru->current_user->getPermission('can_edit_posts') == 'yes') { $can_edit = true; }
if (($hotaru->current_user->getPermission('can_edit_posts') == 'own') && ($hotaru->current_user->id == $user->id)) { $can_edit = true; }

if (strstr($post_orig_url, BASEURL)) { $editorial = true; } // is this an editorial (story with an internal link?)

if (!$can_edit) {
    $hotaru->message = "You don't have permission to edit this post.";
    $hotaru->messageType = "red";
    $hotaru->showMessage();
    return false;
    die();
}

$hotaru->plugins->pluginHook('submit_form_2_assign');

?>

    <div id="breadcrumbs">
        <a href='<?php echo BASEURL; ?>'><?php echo $hotaru->lang['submit_form_home']; ?></a> &raquo; 
        <?php echo $hotaru->lang["submit_edit_post_title"]; ?> &raquo; 
        <a href='<?php echo $hotaru->url(array('page'=>$hotaru->post->id)); ?>'><?php echo $hotaru->post->title; ?></a>
    </div>
        
    <?php echo $hotaru->showMessages(); ?>
            
    
    <?php echo $hotaru->lang["submit_edit_post_instructions"]; ?>

    <form name='submit_edit_post' action='<?php BASEURL; ?>index.php?page=edit_post&sourceurl=<?php echo urlencode($post_orig_url); ?>' method='post'>
    <table>
    <tr>
        <td><?php echo $hotaru->lang["submit_form_url"]; ?>&nbsp; </td>
        <td><?php echo "<a target='_blank' href='" . $post_orig_url . "'>" . $post_orig_url . "</a>"; ?></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><?php echo $hotaru->lang["submit_form_title"]; ?>&nbsp; </td>
        <td><input type='text' size=50 id='post_title' name='post_title' value='<?php echo htmlentities($title_check,ENT_QUOTES,'UTF-8'); ?>'></td>
        <td>&nbsp;</td>
    </tr>
    
    <?php if ($hotaru->post->useContent) { ?>
    
    <tr>
        <td style='vertical-align: top;'><?php echo $hotaru->lang["submit_form_content"]; ?>&nbsp; </td>
        <td colspan=2><textarea id='post_content' name='post_content' rows='6' maxlength='<?php $hotaru->post->contentLength; ?>' style='width: 32em;'><?php echo htmlentities($content_check,ENT_QUOTES,'UTF-8');; ?></textarea></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td colspan=2 style='vertical-align: top;' class="submit_instructions"><?php echo $hotaru->lang['submit_form_allowable_tags']; ?><?php echo htmlentities($hotaru->post->allowableTags); ?></td>
    </tr>
    <?php } ?>
    
    <?php $hotaru->plugins->pluginHook('submit_form_2_fields'); ?>
        
    <?php if ($hotaru->current_user->getPermission('can_edit_posts') == 'yes') { ?>
    <!-- Admin/Mod only options -->
    
    <tr><td colspan=3><u><?php echo $hotaru->lang["submit_edit_post_admin_only"]; ?></u></td></tr>
    
    <?php   if (!$editorial) { // if not editorial, allow source url to be changed: ?>
        <tr>
            <td><?php echo $hotaru->lang["submit_form_url"]; ?>&nbsp; </td>
            <td><input type='text' size=50 id='post_orig_url' name='post_orig_url' value='<?php echo $post_orig_url; ?>'></td>
            <td>&nbsp;</td>
        </tr>
    <?php } ?>
    
    <tr>
        <td style='vertical-align: top;'><?php echo $hotaru->lang["submit_edit_post_status"]; ?>&nbsp; </td>
        <td><select name='post_status'>
            <option value="<?php echo $status_check; ?>"><?php echo $status_check; ?></option>
            <?php 
            $statuses = $hotaru->post->getUniqueStatuses(); 
            if ($statuses) {
                foreach ($statuses as $status) {
                    if ($status != 'unsaved' && $status != 'processing' && $status != $status_check) { 
                        echo "<option value=" . $status . ">" . $status . "</option>\n";
                    }
                }
            }
            ?>
        </td>
    </tr>
    
    <?php $hotaru->plugins->pluginHook('submit_edit_post_admin_fields'); ?>
    <!-- END Admin only options -->
    <?php } ?>
        
    <?php if (($hotaru->current_user->getPermission('can_edit_posts') != 'yes')
                || $editorial) { 
        // use this hidden input to send back the original url when the above form is not used ?>
        <input type='hidden' name='post_orig_url' value='<?php echo $post_orig_url; ?>' />
        <?php $hotaru->plugins->pluginHook('submit_edit_post_non_admin_hidden_fields'); ?>
    <?php } ?>
    
    <input type='hidden' name='from' value='<?php echo $from; ?>' />
    <input type='hidden' name='search_value' value='<?php echo $search_value; ?>' />
    <input type='hidden' name='post_status_filter' value='<?php echo $post_status_filter; ?>' />
    <input type='hidden' name='pg' value='<?php echo $pg; ?>' />
    <input type='hidden' name='post_id' value='<?php echo $post_id ?>' />
    <input type='hidden' name='edit_post' value='true' />
    
    <tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' class='submit' name='submit_edit_post' value='<?php echo $hotaru->lang["submit_edit_post_save"]; ?>' /></td></tr>    
    </table>
    </form>
    
    <?php if ($hotaru->current_user->getPermission('can_delete_posts') == 'yes') { ?>
        <a class='bold_red' href="<?php echo $hotaru->url(array('page'=>'edit_post', 'post_id'=>$post_id, 'action'=>'delete')); ?>">
        <?php echo $hotaru->lang["submit_edit_post_delete"]; } ?>
        </a>
