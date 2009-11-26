<?php
/**
 * ADMIN EMAIL LANGUAGE
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

$lang['admin_email'] = "Admin Email";
$lang["admin_email_instructions"] = "Send an email to all users, user groups or individual users. Each user receives an individually addressed email, and those emails are sent out in batches with a specified time delay between them.";
$lang["admin_email_choose_recipients"] = "Select the recipients from the list box on the right. You can select more than one group or user by holding down the Ctrl key when you click. Use the Shift key to select a range of recipients.";
$lang["admin_email_batch_size"] = "Size of each batch of emails <small>(default: 20)</small>";
$lang["admin_email_pause"] = "Seconds delay between sending batches <small>(default: 10)</small>";
$lang["admin_email_send_to_self"] = "Send a copy to yourself";
$lang["admin_email_send_to_opted_out"] = "Include users who have opted out of receiving admin emails";
$lang["admin_email_send"] = "Send";
$lang["admin_email_sent"] = "Emails sent!";
$lang["admin_email_simulated"] = "Simulation completed";
$lang["admin_email_subject"] = "Subject:";
$lang["admin_email_body"] = "Body:";
$lang["admin_email_body_tip"] = "<b>Tip:</b> Use <i>{username}</i> in the body of the email to represent a user's name.";
$lang["admin_email_simulation"] = "Run a simulation without actually sending any emails <small>(except to yourself if you checked the above box)</small>";
$lang["admin_email_error_size"] = "Error: Batch size must be a numeric value over zero.";
$lang["admin_email_error_pause"] = "Error: Time delay must be a numeric value over zero.";
$lang["admin_email_error_subject"] = "Error: No email subject.";
$lang["admin_email_error_body"] = "Error: No email body.";
$lang["admin_email_error_recipients"] = "Error: No recipients selected.";
$lang["admin_email_pre_remove"] = "-----------------------";
$lang["admin_email_remove"] = "You have received this email because you are registered at " . SITE_NAME .". You can opt out of receiving these emails from your settings page: ";
$lang["admin_email_simulation_mode"] = "Simulation Mode";
$lang["admin_email_real_mode"] = "Real Mode";
$lang["admin_email_sent_to"] = "Email sent to ";
$lang["admin_email_fake_sending"] = "Faked sending an email to ";
$lang["admin_email_email_batch"] = "Email Batch: ";
$lang["admin_email_waiting"] = "Waiting ";
$lang["admin_email_before_next_batch"] = " seconds before sending the next batch...";
$lang["admin_email_sent_to_self"] = "An email was sent to you, but no other emails were sent.";
$lang["admin_email_after_simulation"] = "The simulation has finished. Return to ";
$lang["admin_email_after_simulation2"] = ", uncheck the \"Simulation\" box and send the emails for real.";
$lang["admin_email_after_real"] = "All emails have been sent. Return to ";
$lang["admin_email_after_real2"] = ". Your settings and message were saved so you can use them next time.";
$lang["admin_email_redirecting"] = "Running the first batch and redirecting to the results...";
$lang["admin_email_abort"] = "Abort";
$lang["admin_email_no_recipients"] = "Complete. If no emails were sent, it's possible the selected users have opted out of recieving emails from admins.";

/* User Settings */
$lang['users_settings_email_from_admin'] = "Allow email from site admins?";

?>