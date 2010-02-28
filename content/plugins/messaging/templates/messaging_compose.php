<?php
/**
 * Messaging compose new message
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

<div id="messaging_compose" class="users_content">

<h2><?php echo $h->lang["messaging_compose"]; ?></h2>

<?php if ($h->vars['message_reply']) { ?>
    <?php echo $h->lang['messaging_in_reply_to']; ?>
    <a href="<?php echo BASEURL; ?>index.php?page=show_message&amp;id=<?php echo $h->vars['message_id']; ?>" target="_blank">
        <?php echo $h->vars['message_subject']; ?>
    </a>
<?php } ?>
    
<?php echo $h->showMessages(); ?>

<form name="compose_message" action="<?php echo BASEURL; ?>index.php?page=compose&amp;action=send" method="post">
    <table>
        <tr>
            <td>
                <?php echo $h->lang['messaging_to']; ?>
            </td>
            <td>
                <input id="message_to" name="message_to" type="text" value="<?php echo $h->vars['message_to']; ?>" />
                <small><?php echo $h->lang['messaging_username']; ?></small>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $h->lang['messaging_subject']; ?>
            </td>
            <td>
                <?php if ($h->vars['message_reply']) { 
                    $h->vars['message_subject'] = $h->lang["messaging_re"] . $h->vars['message_subject'];
                } ?>
                <input id="message_subject" name="message_subject" type="text" value="<?php echo $h->vars['message_subject']; ?>" />
            </td>
        </tr>
        <tr>
            <td><?php echo $h->lang['messaging_body']; ?></td>
            <td><textarea id="message_body" name="message_body" rows="10" /><?php echo $h->vars['message_body']; ?></textarea></td>
        </tr>
    </table>
    
    <br/>
    <p align="right"><input class="submit" type="submit" name="send" value="<?php echo $h->lang['messaging_send']; ?>" /></p>
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>

</div>