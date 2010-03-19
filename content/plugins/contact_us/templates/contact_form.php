<?php
/**
 * Contact Form
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

<div id="contact_form">
    
    <h2><?php echo $h->lang["contact_us"]; ?></h2>
    
    <p><?php echo $h->lang['contact_us_description']; ?></p>
    
    <?php echo $h->showMessages(); ?>

    <form name='contact_us_form' action='<?php echo $h->url(array('page'=>'contact')); ?>' method='post'>
    <table>
        <tr>
            <td>
                <?php echo $h->lang['contact_us_name']; ?>
            </td>
            <td>
                <input type='text' size=30 name='name' value='<?php echo $h->vars['contact_us_name']; ?>' />
            </td>
        </tr>
        
        <tr>
            <td>
                <?php echo $h->lang['contact_us_email']; ?>
            </td>
            <td>
                <input type='text' size=30 name='email' value='<?php echo $h->vars['contact_us_email']; ?>' />
            </td>
        </tr>
        
        <tr>
            <td>
                <?php echo $h->lang['contact_us_message']; ?>
            </td>
            <td>
                <textarea cols=60 rows=20 name='body'><?php echo $h->vars['contact_us_body']; ?></textarea>
            </td>
        </tr>
        
        <?php if ($h->vars['contact_us_recaptcha']) { ?>
            <tr>
            <td>&nbsp;</td>
                <td>
                    <?php $h->pluginHook('show_recaptcha'); ?>
                </td>
            </tr>
        <?php } ?>
        
        <tr>
            <td>&nbsp;</td>
            <td>
                <input type='checkbox' name='self' value='self' <?php echo $h->vars['contact_us_self']; ?>>&nbsp;&nbsp;
                <?php echo $h->lang['contact_us_send_self']; ?>
            </td>
        
        <tr>
            <td>&nbsp;</td>
            <td style='text-align:right;'>
                <input type='submit' class='submit' value='<?php echo $h->lang['contact_us_send']; ?>' />
            </td>
        </tr>
    </table>
    <input type='hidden' name='submitted' value='contact' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    </form>
    
</div>
