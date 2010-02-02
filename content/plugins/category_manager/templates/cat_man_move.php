<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_move.php
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
 
    <h2><?php echo $h->lang["cat_man_move"]; ?></h2>

    <table class="cat_man_table">
    <tr><td class="cat_man_body">
        <?php echo $h->lang["cat_man_move_instruct"]; ?> <br /><br />
        <?php
        foreach ($h->vars['the_cats'] as $cat) {
            if ($cat['category_safe_name'] != "all") {
                if ($cat['category_parent'] > 1) {
                    for($i=1; $i<$cat['category_level']; $i++) {
                        echo "--- ";
                    }
                    echo "<a href='#' class='cat_man_drop_down' title='Show options'>" . $cat['category_name'] . "</a><br />";
                } else {
                    echo "<a href='#' class='cat_man_drop_down' title='Show options'>" . $cat['category_name'] . "</a><br />";
                }
        ?>
                <div class='cat_move_options' style='display:none'>
                    <form class='cat_man_move_form' style='margin-bottom: 0px;' name='category_manager_move_form' action='<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=move_save&amp;id=<?php echo $cat['category_id']; ?>' method='post'>
                            <?php echo $h->lang["cat_man_move_put"]; ?> '<?php echo $cat['category_name']; ?>'   
                            <select name="placement">";
                                <option value="after"><?php echo $h->lang["cat_man_move_after"]; ?></option>
                                <option value="before"><?php echo $h->lang["cat_man_move_before"]; ?></option>
                                <option value="aschild"><?php echo $h->lang["cat_man_move_in"]; ?></option>
                            </select> 
                            <select name="parents">
                            <?php
                            foreach ($h->vars['the_cats'] as $cat2) {
                                if ($cat2['category_safe_name'] != "all") {
                                    if ($cat2['category_id'] != $cat['category_id']) {
                                        if ($cat['category_parent'] == 1 && $cat2['category_parent'] == 1) {
                                              echo "<option value='" . $cat2['category_id'] . "'>" . $cat2['category_name'] . "</option>";
                                        } elseif ($cat['category_parent'] != 1 && ($cat['category_parent'] == $cat2['category_parent'])) {
                                              echo "<option value='" . $cat2['category_id'] . "'>" . $cat2['category_name'] . "</option>";
                                        }
                                    }
                                }
                              }
                              ?>
                              </select>
                        <input type='submit' name='save_form1' value='<?php echo $h->lang["cat_man_go"]; ?>'>
                        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
                    </form>
                    <?php if ($cat['category_parent'] > 1) { ?>
                        <?php echo $h->lang["cat_man_move_or"]; ?> 
                        <form class='cat_man_move_form' style='margin-bottom: 0px;' name='category_manager_move_form2' action='<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=move_save&amp;id=<?php echo $cat['category_id']; ?>' method='post'>
                            <?php echo $h->lang["cat_man_move_move"]; ?>  '<?php echo $cat['category_name']; ?>' 
                            <?php echo $h->lang["cat_man_move_to"]; ?> 
                            <select name='moveup'>
                            <option value='top'><?php echo $h->lang["cat_man_move_top_level"]; ?> </option>
                            <?php
                            foreach ($h->vars['the_cats'] as $cat3) {
                                if ($cat3['category_safe_name'] != "all") {
                                    if ($cat3['category_parent'] == 1 && ($cat['category_parent'] != $cat3['category_parent'])) {
                                          echo "<option value='" . $cat3['category_id'] . "'>" . $cat3['category_name'] . "</option>";
                                    }
                                }
                              }
                              ?>
                              </select>
                            <input type='submit' name='save_form2' value='<?php echo $h->lang["cat_man_go"]; ?>'>
                            <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
                        </form>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
        <br />
    </td>
    
    <td class="cat_man_menu_holder">
        <div>
            <h3><?php echo $h->lang["cat_man_menu_title"]; ?></h3>
            <?php $h->displayTemplate('cat_man_menu', 'category_manager'); ?>
            
            <h3><?php echo $h->lang["cat_man_category_tree"]; ?></h3>
            <?php
                $tree = new CategoryManagerSettings($h); 
                $tree->tree($h, $h->vars['the_cats']);
            ?>

        </div>
    </td>
    </tr></table>
