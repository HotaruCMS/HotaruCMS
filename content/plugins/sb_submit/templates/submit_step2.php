<?php
/**
 * Template for Submit: Submit Step 2
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
/*
if ($hotaru->cage->post->getAlpha('submit2') == 'true') {
    // Submitted this form...
    $title_check = $hotaru->cage->post->noTags('post_title');    
    $content_check = sanitize($hotaru->cage->post->getHtmLawed('post_content'), 2, $hotaru->post->allowableTags);
    $content_check = stripslashes($content_check);
    $post_orig_url = $hotaru->cage->post->testUri('post_orig_url');
    $post_id = $hotaru->cage->post->getInt('post_id');    
    $hotaru->post->id = $post_id;
    
} elseif ($hotaru->cage->post->getAlpha('submit3') == 'edit') {
    // Come back from step 3 to make changes...
    $title_check = $hotaru->post->title;
    $content_check = $hotaru->post->content;
    $post_orig_url = $hotaru->post->origUrl;
    $post_id = $hotaru->post->id;
} else {
    // First time here...
*/

/*
    $title_check = $hotaru->vars['post_orig_title'];
    $content_check = "";
    $post_id = 0;
}
*/

$hotaru->pluginHook('submit_2_assign');

?>
    <?php $hotaru->showMessages(); ?>
    
    <?php echo $hotaru->lang["submit_instructions_2"]; ?>

    <form name='submit_2' action='<?php BASEURL; ?>index.php?page=submit2' method='post'>
    <table>
    
    <?php if ($hotaru->vars['submit_use_link']) { // only show if posting a link ?>
        <tr>
            <td><?php echo $hotaru->lang["submit_url"]; ?>&nbsp; </td>
            <td><?php echo $hotaru->vars['submit_orig_url']; ?></td>
            <td>&nbsp;</td>
        </tr>
    <?php } ?>
    
    <tr>
        <td><?php echo $hotaru->lang["submit_title"]; ?>&nbsp; </td>
        <td><input type='text' id='post_title' name='post_title' value='<?php echo $hotaru->vars['submit_title']; ?>'></td>
        <td id='ajax_loader'>&nbsp;</td>
    </tr>
    
    <?php if ($hotaru->vars['submit_use_content']) { ?>
    <tr>
        <td style='vertical-align: top;'><?php echo $hotaru->lang["submit_content"]; ?>&nbsp; </td>
        <td colspan='2'>
            <textarea id='post_content' name='post_content' rows='6' maxlength='<?php $hotaru->vars['submit_content_length']; ?>'>
            <?php echo $hotaru->vars['submit_content']; ?></textarea>
        </td>
    </tr>
    
    <tr>
        <td>&nbsp;</td>
        <td colspan=2 style='vertical-align: top;' class="submit_instructions">
            <?php echo $hotaru->lang['submit_allowable_tags']; ?>
            <?php echo $hotaru->vars['submit_allowable_tags']; ?>
        </td>
    </tr>
    <?php } ?>
    
    <?php $hotaru->pluginHook('submit_2_fields'); ?>
            
    <input type='hidden' name='submit_orig_url' value='<?php echo $hotaru->vars['submit_orig_url']; ?>' />
    <input type='hidden' name='submit_post_id' value='<?php echo $hotaru->vars['submit_post_id']; ?>' />
    <input type='hidden' name='submit2' value='true' />
    <input type='hidden' name='submit_key' value='<?php echo $hotaru->vars['submit_key']; ?>' />
    <input type='hidden' name='csrf' value='<?php echo $hotaru->csrfToken; ?>' />
    
    <tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' onclick="javascript:safeExit=true;" class='submit' name='submit' value='<?php echo $hotaru->lang['main_form_next']; ?>' /></td></tr>    
    </table>
    </form>
