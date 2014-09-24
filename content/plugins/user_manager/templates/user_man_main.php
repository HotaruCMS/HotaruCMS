<?php
/**
 * Plugin name: User Manager
 * Template name: plugins/user_manager/user_man_main.php
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

// fixes for undefined index errors:
if (!isset($h->vars['user_man_rows'])) { $h->vars['user_man_rows'] = ''; }
if (!isset($h->vars['user_man_navi'])) { $h->vars['user_man_navi'] = ''; }
?>

<?php echo $h->lang["user_man_desc"]; ?>

<?php echo " [<a href='" . BASEURL . "admin_index.php?user_filter=pending&plugin=user_manager&page=plugin_settings&type=filter'>" . $h->lang["user_man_num_pending"] . $h->vars['num_pending'] . "</a>]"; ?>

<p id="user_man_navigation">
    <b><u><?php echo $h->lang["user_man"]; ?></u></b> &nbsp;&nbsp;
    <?php echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_perms'>" . $h->lang["user_man_default_perms"] . "</a>"; ?> &nbsp;&nbsp;
    <?php echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_settings'>" . $h->lang["user_man_default_settings"] . "</a>"; ?> &nbsp;&nbsp;
    <?php echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=add_user'>" . $h->lang["user_man_add"] . "</a>"; ?>
</p>

<?php echo $h->showMessage(); ?>

<table><tr><td>

<form name='user_man_search_form' action='<?php echo BASEURL; ?>admin_index.php' method='get'>
    <h4><?php echo $h->lang["user_man_search"]; ?></h4>

	<input type='text' size=30 name='search_value' value='<?php echo $h->vars['search_term']; ?>' />
	<input class='submit btn' type='submit' value='<?php echo $h->lang['user_man_search_button']; ?>' />

    <input type='hidden' name='plugin' value='user_manager' />
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='type' value='search' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>

</td><td>

<form name='user_man_filter_form' action='<?php echo BASEURL; ?>admin_index.php?plugin=user_manager' method='get'>
    <h4><?php echo $h->lang["user_man_filter"]; ?></h4>

       <select name='user_filter'>
            <option style='font-weight: bold;' value='<?php echo $h->vars['user_filter']; ?>'><?php echo make_name($h->vars['user_filter'], '_'); ?></option>
            <option value='' disabled>-----</option>
            <option value='all'><?php echo $h->lang['user_man_filter_all']; ?></option>
            <option value='not_killspammed'><?php echo $h->lang['user_man_filter_not_killspammed']; ?></option>
            <option value='' disabled>-----</option>
            <option value='newest'><?php echo $h->lang['user_man_filter_newest']; ?></option>
            <option value='oldest'><?php echo $h->lang['user_man_filter_oldest']; ?></option>
            <option value='last_visited'><?php echo $h->lang['user_man_filter_last_visited']; ?></option>
            <option value='' disabled>-----</option>
	<option value='content_pending'><?php echo $h->lang['user_man_filter_content_pending']; ?></option>
            <option value='' disabled>-----</option>
            <?php 
            if ($h->vars['roles']) {
                foreach ($h->vars['roles'] as $status) {
                    if ($status != 'unsaved' && $status != 'deleted') { 
                        echo "<option value=" . $status . ">" . make_name($status, '_') . "</option>\n";
                    }
                }
            }
            ?>
        </select>

        <select name='um_limit'>
			<?php $values = array(20, 30, 50, 100, 250);
				foreach ($values as $v) {
					$selected = ($v == $h->vars['um_limit']) ? "selected='selected'" : "";
					echo "<option $selected>$v</option>";
				}
			?>
        </select>

        <input class='submit btn' type='submit' value='<?php echo $h->lang['user_man_filter_button']; ?>' />

    <input type='hidden' name='plugin' value='user_manager' />
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='type' value='filter' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>

</tr></table>

<form name='user_man_checkbox_form' style='margin: 0px; padding: 0px;' action='<?php echo BASEURL; ?>admin_index.php?plugin=user_manager' method='get'>
    
	<div id="table_list">
	    <table class="table table-bordered">
	    <tr class='table_headers info'>
	        <td><?php echo $h->lang["user_man_id"]; ?></td>
	        <td><?php echo $h->lang["user_man_role"]; ?></td>
	        <td><?php echo $h->lang["user_man_username"]; ?></td>
	        <td><?php echo $h->lang["user_man_joined"]; ?></td>
	        <td><?php echo $h->lang["user_man_account"]; ?></td>
	        <td><?php echo $h->lang["user_man_perms"]; ?></td>
	        <td><input type="checkbox" name="checkall" id="checkall"></td>
	    </tr>
	            <?php echo $h->vars['user_man_rows']; ?>
	    </table>
	</div>
	
	<?php echo $h->vars['user_man_navi']; // pagination ?>
	<br />

	<div class="row">
	
		<div class='span7 offset1 user_man_pre_submit well'>
		    <p class="user_man_pre_submit_instruct"><?php echo $h->lang['user_man_when_killspam_delete']; ?></p>
		    <input type='checkbox' name='addblockedlist'> 
		    <?php echo $h->lang['user_man_add_blocked_list']; ?>
		    <?php $h->pluginHook('user_manager_pre_submit_button'); ?>
		</div>

		<div class='span4 user_man_submit_button'>
	
	        <select name='checkbox_action'>
	            <option value='member'><?php echo $h->lang["user_man_set_member"]; ?></option>
	            <option value='moderator'><?php echo $h->lang["user_man_set_moderator"]; ?></option>
	            <option value='supermod'><?php echo $h->lang["user_man_set_supermod"]; ?></option>
	            <option value='admin'><?php echo $h->lang["user_man_set_admin"]; ?></option>
	            <option value='' disabled>-----</option>
	
				<?php 
					$roles = $h->getRoles('custom');
					if ($roles) {
						foreach ($roles as $r) {
							echo "<option value=" . $r . ">" . $h->lang["user_man_set_role"] . "'" . $r . "'</option>\n";
						}
						echo "<option value='' disabled>-----</option>\n";
					}
				?>
	
	            <option value='undermod'><?php echo $h->lang["user_man_set_undermod"]; ?></option>
	            <option value='pending'><?php echo $h->lang["user_man_set_pending"]; ?></option>
	            <option value='suspended'><?php echo $h->lang["user_man_set_suspended"]; ?></option>
	            <option value='banned'><?php echo $h->lang["user_man_set_banned"]; ?></option>
	            <option value='' disabled>-----</option>
	            <option style='color: red; font-weight: bold;' value='killspammed'><?php echo $h->lang["user_man_set_killspammed"]; ?></option>
	            <option value='' disabled>-----</option>
	            <option style='color: red; font-weight: bold;' value='deleted'><?php echo $h->lang["user_man_set_delete"]; ?></option>
	        </select>
	
			<input class='submit btn' type='submit' value='<?php echo $h->lang['user_man_checkbox_action']; ?>' />
	
	        <input type='hidden' name='plugin' value='user_manager' />
	        <input type='hidden' name='page' value='plugin_settings' />
			<input type='hidden' name='user_filter' value='<?php echo $h->vars['user_filter']; ?>' />
			<input type='hidden' name='pg' value='<?php echo $h->cage->get->testInt('pg'); ?>' />
			<input type='hidden' name='um_limit' value='<?php echo $h->vars['um_limit']; ?>' />
			<?php if ($h->cage->get->sanitizeTags('search_value')) { ?>
				<input type='hidden' name='search_value' value='<?php echo $h->cage->get->sanitizeTags('search_value'); ?>' />
			<?php } ?>
	        <input type='hidden' name='type' value='checkboxes' />
	        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
		</div>
		
	</div>
</form>
