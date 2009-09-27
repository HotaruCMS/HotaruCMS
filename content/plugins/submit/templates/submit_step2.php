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
 
global $hotaru, $cage, $lang, $post, $plugins, $post_orig_url, $post_orig_title;
                
if ($cage->post->getAlpha('submit2') == 'true') {
    // Submitted this form...
    $title_check = $cage->post->noTags('post_title');    
    $content_check = sanitize($cage->post->getPurifiedHTML('post_content'), 2, $post->getAllowableTags());    
    $post_id = $cage->post->getInt('post_id');    
    $post->setId($post_id);
    
} elseif ($cage->post->getAlpha('submit3') == 'edit') {
    // Come back from step 3 to make changes...
    $title_check = $post->getTitle();
    $content_check = $post->getContent();
    $post_orig_url = $post->getOrigUrl();
    $post_id = $post->getId();
} else {
    // First time here...
    $title_check = $post_orig_title;
    $content_check = "";
    $post_id = 0;
}

$plugins->pluginHook('submit_form_2_assign');

?>

    <div id="breadcrumbs"><a href='<?php echo BASEURL; ?>'><?php echo $lang['submit_form_home']; ?></a> &raquo; <?php echo $lang["submit_form_step2"]; ?></div>
        
    <?php echo $hotaru->showMessages(); ?>
            
    
    <?php echo $lang["submit_form_instructions_2"]; ?>

    <form name='submit_form_2' action='<?php BASEURL; ?>index.php?page=submit2&sourceurl=<?php echo $post_orig_url; ?>' method='post'>
    <table>
    <tr>
        <td><?php echo $lang["submit_form_url"]; ?>&nbsp; </td>
        <td><?php echo $post_orig_url; ?></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><?php echo $lang["submit_form_title"]; ?>&nbsp; </td>
        <td><input type='text' size=50 id='post_title' name='post_title' value='<?php echo $title_check; ?>'></td>
        <td id='ajax_loader'>&nbsp;</td>
    </tr>
    
    <?php if ($post->getUseContent()) { ?>
    <tr>
        <td style='vertical-align: top;'><?php echo $lang["submit_form_content"]; ?>&nbsp; </td>
        <td colspan='2'><textarea id='post_content' name='post_content' rows='6' maxlength='<?php $post->getContentLength(); ?>' style='width: 32em;'><?php echo $content_check; ?></textarea></td>
    </tr>
    <?php } ?>
    
    <?php $plugins->pluginHook('submit_form_2_fields'); ?>
            
    <input type='hidden' name='post_orig_url' value='<?php echo $post_orig_url; ?>' />
    <input type='hidden' name='post_id' value='<?php echo $post_id; ?>' />
    <input type='hidden' name='submit2' value='true' />
    
    <tr><td colspan=3>&nbsp;</td></tr>
    <tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' onclick="javascript:safeExit=true;" name='submit' value='<?php echo $lang['submit_form_submit_next_button']; ?>' /></td></tr>    
    </table>
    </form>
