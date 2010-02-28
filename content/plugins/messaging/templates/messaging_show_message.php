<?php
/**
 * Messaging show message
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
$h->setAvatar($h->vars['message_from_id'], 16);  
?>

<div id="messaging_show_message" class="users_content">

<h2><?php echo $h->lang["messaging_view_message"]; ?></h2>
    
<?php echo $h->showMessages(); ?>

    <table>
        <tr>
            <td>
                <?php echo $h->lang['messaging_from']; ?>
            </td>
            <td>
                <?php 
                if($h->isActive('avatar')) {
                    $h->setAvatar($h->currentUser->id, 16);
                    echo $h->linkAvatar();
                }
                ?>
                <a href="<?php echo $h->url(array('user'=>$h->vars['message_from_name'])); ?>"><?php echo $h->vars['message_from_name']; ?></a>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $h->lang['messaging_date']; ?>
            </td>
            <td>
                <?php echo $h->vars['message_date']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $h->lang['messaging_subject']; ?>
            </td>
            <td>
                <?php echo $h->vars['message_subject']; ?>
            </td>
        </tr>
        <tr>
            <td><?php echo $h->lang['messaging_body']; ?></td>
            <td><?php echo $h->vars['message_body']; ?></td>
        </tr>
    </table>
    
    <br/>
    <p align="center"><a href="<?php echo BASEURL; ?>index.php?page=compose&amp;reply=<?php echo $h->vars['message_id']; ?>"><?php echo $h->lang["messaging_reply"]; ?></a></p>

</div>