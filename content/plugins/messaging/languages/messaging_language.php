<?php
/**
 * MESSAGING LANGUAGE
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

/* Messaging */
$lang["messaging_messages"] = "Messages: ";
$lang["messaging_inbox"] = "Inbox";
$lang["messaging_outbox"] = "Outbox";
$lang["messaging_compose"] = "New Message";
$lang["messaging_send_message"] = "Send Message";
$lang["messaging_id"] = "ID";
$lang["messaging_to"] = "To:";
$lang["messaging_from"] = "From:";
$lang["messaging_subject"] = "Subject:";
$lang["messaging_body"] = "Body:";
$lang["messaging_send"] = "Send";
$lang["messaging_sent"] = "Your message has been sent";
$lang["messaging_date"] = "Date:";
$lang["messaging_find_user"] = "Find a user: ";
$lang["messaging_block"] = "Block";
$lang["messaging_delete"] = "Delete";
$lang["messaging_delete_selected"] = "Delete Selected";
$lang['messaging_no_messages'] = "No messages";
$lang["messaging_username"] = "(username)";
$lang["messaging_no_to"] = "Please complete the \"To\" field";
$lang["messaging_no_subject"] = "Please complete the \"Subject\" field";
$lang["messaging_no_body"] = "Please complete the \"Body\" field";
$lang["messaging_no_user"] = "That user isn't registered";
$lang["messaging_reply"] = "Reply to this message";
$lang["messaging_in_reply_to"] = "In reply to: ";
$lang["messaging_re"] = "Re: ";
$lang["messaging_view_message"] = "Viewing Message";
$lang["messaging_view_message_unauthorized"] = "Sorry, you're not allowed to view this message.";

/* Email notification */
$lang['messaging_email_subject'] = "New message from " . SITE_NAME;
$lang['messaging_email_greeting'] = "Hi ";
$lang['messaging_email_message'] = "You've been sent a private message from ";
$lang['messaging_email_no_reply'] = "*** PLEASE DON'T REPLY TO THIS EMAIL ***";
$lang['messaging_email_reply_here'] = "You can reply to the message on " . SITE_NAME . " here: ";
$lang['messaging_email_thank_you'] = "Thank you,";
$lang['messaging_email_site_admin'] = SITE_NAME . " Admin";

/* Maintenance */
$lang["messaging_maintenance_clear_messages"] = "Clean messages table";
$lang["messaging_maintenance_clear_messages_desc"] = "Remove messages deleted from both the sender's outbox <i>and</i> the recipient's inbox.";
$lang['messaging_maintenance_table_cleaned'] = "Messaging table cleaned and optimized";

/* User Settings */
$lang['users_settings_pm_notification_by_email'] = "Message notification by email?";

/* Announcement */
$lang['messaging_unread_messages_announcement'] = "You have unread messages in your inbox!";

?>