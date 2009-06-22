<?php

/* Installation */

/* Common language */
$lang["install_title"] = "Hotaru CMS Setup";
$lang["install_next"] = "Next";
$lang["install_back"] = "Back";
$lang["install_trouble"] = "Having trouble? Visit the forums at <a href='http://hotarucms.org'>HotaruCMS.org</a> for help.";

/* Step 1 */
$lang["install_step1"] = "Step 1/6: Welcome";
$lang["install_step1_welcome"] = "Welcome to Hotaru CMS. Click \"Next\" to begin setting up your site...";

/* Step 2 */
$lang["install_step2"] = "Step 2/6: Database Setup";
$lang["install_step2_instructions"] = "To set up a database for Hotaru CMS, you'll need to do the following";
$lang["install_step2_instructions1"] = "Create a database in your web host's control panel. Make a note of your username and password!";
$lang["install_step2_instructions2"] = "Open <pre>HotaruCMS/hotaru_settings.php</pre> and fill in the \"Database Details\" section.";
$lang["install_step2_instructions3"] = "Save and upload <pre>hotaru_settings.php</pre> to your server, then click \"Next\"...";
$lang["install_step2_warning"] = "<b>Warning</b>";
$lang["install_step2_warning_note"] = "When you click \"Next\", new database tables will be created, deleting any old ones you may have!";


/* Step 3 */
$lang["install_step3"] = "Step 3/6: Create Database Tables";
$lang["install_step3_creating_table"] = "Creating table";
$lang["install_step3_already_exists"] = "It seems there are already tables for Hotaru CMS in the database.";
$lang["install_step3_continue"] = "Click \"Next\" to continue.";
$lang["install_step3_rebuild_note"] = "<i>Note</i>: If you'd like to start fresh, ";
$lang["install_step3_rebuild_link"] = "delete and rebuild the database tables";
$lang["install_step3_success"] = "Database tables created successfully. Click \"Next\" to configure Hotaru CMS.";

/* Step 4 */
$lang["install_step4"] = "Step 4/6: Names, Paths and Themes";
$lang["install_step4_instructions"] = "Remember <pre>hotaru_settings.php</pre>? If you haven't already, you'll need to fill in the other details now";
$lang["install_step4_instructions1"] = "Give your site a <pre>sitename</pre>, e.g \"My Fantastic Hotaru Site\".";
$lang["install_step4_instructions2"] = "Fill in the <pre>baseurl</pre>, e.g. http://www.myfantastichotarusite.com/. Don't forget the trailing slash (/)";
$lang["install_step4_instructions3"] = "If you want to use a custom theme instead of the default one, change <i>current_theme</i> to match the folder name of your custom theme.";
$lang["install_step4_instructions4"] = "Save and upload <pre>hotaru_settings.php</pre> to your server, then click \"Next\"...";

/* Step 5 */
$lang["install_step5"] = "Step 5/6: Admin Registration";
$lang["install_step5_instructions"] = "Register yourself as a site administrator";

/* Step 6 */
$lang["install_step6"] = "Step 6/6: Completion";
$lang["install_step6_installation_complete"] = "Installation has been successfully completed.";
$lang["install_step6_installation_delete"] = "You <b>must</b> delete the install folder or someone else could run the install script and wipe everything!";
$lang["install_step6_installation_go_play"] = "Done? Okay, go and play with your new Hotaru site!";
$lang["install_home"] = "Visit";

?>