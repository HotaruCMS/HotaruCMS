<?php
/**
 * Template for Submit: Submit Step 1
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
 
$submitted_url = urldecode($h->vars['submitted_data']['submit_orig_url']);
?>

    <?php echo $h->showMessage(); ?>
            

    <?php echo $h->lang["submit_instructions_1"]; ?>
    
    <form name='submit_1' action='<?php echo BASEURL; ?>index.php?page=submit1' method='post'>
    <table>
    <tr>
        <td><?php echo $h->lang["submit_url"]; ?>&nbsp; </td>
        <td><input type='text' id='submit_orig_url' name='submit_orig_url' value='<?php echo $submitted_url; ?>' /></td>
        <td>&nbsp;</td>
    </tr>
    <?php if ($h->currentUser->getPermission('can_post_without_link') == 'yes') { ?>
    <tr>
        <td colspan = 3><input type='checkbox' name='no_link'>&nbsp;
            <?php echo $h->lang["submit_post_without_link"]; ?></td>
    </tr>
    <?php } ?>

    <input type='hidden' name='submit1' value='true' />

    <tr><td colspan=3>&nbsp;</td></tr>
    <tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' class='submit' name='submit' value='<?php echo $h->lang['main_form_next']; ?>' /></td></tr>    
    </table>
    </form>

