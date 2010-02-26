<?php
/**
 * TOS Anti Spam register form
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
<tr>
    <td colspan='2'>
        <input type='checkbox' name='tos_check' value='tos_check' <?php echo $h->vars['tos_check']; ?> />&nbsp;&nbsp;<?php echo $h->lang["tos_antispam_tos"]; ?> 
    </td>
</tr>

<tr>
    <td colspan='2'>
        <?php echo $h->vars["tos_question"]; ?> 
        <select name='tos_answer'>
        <?php if ($h->vars['tos_answer_selected'] == "choose") { ?>
            <option value="choose" selected="selected"><?php echo $h->lang["tos_antispam_select"]; ?></option>
        <?php } ?>
        <?php foreach ($h->vars['tos_choices'] as $key => $value) { ?>
            <?php if ($h->vars['tos_answer_selected'] == $key) { $selected = "selected"; } else { $selected = ""; } ?>
            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
        <?php } ?>
        </select>
    </td>
</tr>