<?php
/**
 * Messaging outbox
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

<div id="messaging_outbox" class="users_content">

<h2><?php echo $h->lang["messaging_outbox"]; ?></h2>
    
<?php echo $h->showMessages(); ?>

<form name="outbox_form" action="<?php echo BASEURL; ?>index.php?page=outbox" method="post">
<table class="messaging_list">
    <tr class="messaging_list_headers">
        <td class="messaging_fom"><?php echo $h->lang['messaging_to']; ?></td>
        <td class="messaging_subject"><?php echo $h->lang['messaging_subject']; ?></td>
        <td class="messaging_date"><?php echo $h->lang['messaging_date']; ?></td>
        <td class="messaging_delete"></td>
    </tr>
    
    <?php if (isset($h->vars['messages_list']->list)) { ?>

        <?php foreach ($h->vars['messages_list']->items as $msg) { ?>
            <tr id="<?php echo $msg->message_id; ?>">
            
                <?php $name = $h->getUserNameFromId($msg->message_to); ?>
                <td class="messaging_from"><a href="<?php echo $h->url(array('user'=>$name)); ?>"><?php echo $name; ?></a></td>
                
                <td class="messaging_subject">
                    <a href="<?php echo BASEURL; ?>index.php?page=show_message&amp;id=<?php echo $msg->message_id; ?>">
                        <?php echo sanitize(urldecode($msg->message_subject), 'all'); ?>
                    </a>
                </td>
                
                <td class="messaging_date"><?php echo date('j M \'y', strtotime($msg->message_date)); ?></td>
                
                <td class="messaging_delete"><center><input type="checkbox" name="message[<?php echo $msg->message_id; ?>]" id="message-<?php echo $msg->message_id; ?>" value="delete"></center></td>
                
            </tr>
        <?php } ?>
    
    <?php } else { ?>
        <tr><td colspan='4'><center><?php echo $h->lang['messaging_no_messages']; ?></center></td></tr>
    <?php } ?>
    
</table>

    <?php echo $h->pageBar($h->vars['messages_list']); ?> 
    
    <br/>
    <p align="right"><input type="submit" name="delete_selected" value="<?php echo $h->lang['messaging_delete_selected']; ?>" /></p>

</form>

</div>