<?php
/**
 * Vote Button - Alert message
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

$flags = $hotaru->vars['flag_count'];
?>

<?php if ($flags > 1) { ?>

    <img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/flag_red.png"
    title="<?php echo $hotaru->lang["vote_alert_flagged_message_1"] . " " . $flags . " " . $hotaru->lang["vote_alert_flagged_message_users"]  . " " . $hotaru->lang["vote_alert_flagged_message_2"] . " " . $hotaru->vars['flag_why']; ?>">
    
<?php } else { ?>

    <img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/flag_yellow.png"
    title="<?php echo $hotaru->lang["vote_alert_flagged_message_1"] . " " . $flags . " " . $hotaru->lang["vote_alert_flagged_message_user"]  . " " . $hotaru->lang["vote_alert_flagged_message_2"] . " " . $hotaru->vars['flag_why']; ?>">
<?php } ?>