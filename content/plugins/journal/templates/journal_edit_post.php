<?php
/**
 * Comment Form
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

<div class="post_form" style="display: none;">
    <form name='edit_post_form' action='<?php echo $h->url(array('page'=>'journal')); ?>' method='post' onsubmit="document.getElementById('post_submit_<?php echo $h->post->id; ?>').disabled = true; return true;">
        <input type="text" name="post_title" id="post_title_<?php echo $h->post->id; ?>" value="<?php echo htmlentities($h->post->title,ENT_QUOTES,'UTF-8'); ?>" /><?php echo $h->lang['journal_title']; ?><br />
        
        <textarea name="post_content" id="post_content_<?php echo $h->post->id; ?>" rows="6" cols="50"></textarea><br />
        
        <div class="form_submit">
            <input type="submit" name="submit" id="post_submit_<?php echo $h->post->id; ?>" value="<?php echo $h->lang['journal_form_submit']; ?>" class="submit" />
        </div>
        
        <div class="post_instructions"><?php echo $h->lang['journal_form_allowable_tags']; ?><?php echo htmlentities($h->post->allowableTags); ?></div>
        
        <div class="clear">&nbsp;</div>
        
        <input type="hidden" name="post_process" id="post_process_<?php echo $h->post->id; ?>" value="newpost" />
        <input type="hidden" name="post_author" value="<?php echo $h->post->author; ?>" />
        <input type="hidden" name="post_id" value="<?php echo $h->post->id; ?>" />
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    </form>
</div>

