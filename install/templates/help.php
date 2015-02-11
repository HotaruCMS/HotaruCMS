<?php
    $modules = get_loaded_extensions();
    
    $required = array(
        'mysqli'=>'http://php.net/manual/en/book.mysqli.php',
        'filter'=>'http://php.net/manual/en/book.filter.php',
        'curl'=>'http://php.net/manual/en/book.curl.php',
        'mbstring'=>'http://www.php.net/manual/en/book.mbstring.php'
    );
    
    
?>

<!-- Step title -->
		<legend><?php echo $lang['install_help']; ?></legend>

		<div class="container">

			<div class="row">
				<div class="col-md-9">
                                    
                                    <div class='panel panel-default'>
                                        <div class="panel-heading">Your System</div>
                                        <div class="panel-body">
                                            <span class="label label-primary">PHP v <?php echo phpversion(); ?></span>
                                            <?php
                                            $php_module_not_found = false;
                                            
                                            foreach ($required as $module => $url) {
                                                if (!in_array($module, $modules)) {
                                                    echo '<span class="label label-danger">' . $module . '</span> ';
                                                    //$h->messages[$lang['install_step4_form_check_php_warning'] . '<a href="' . $url . '" target="_blank">' . $module . '</a><br/>'] = 'red';
                                                    $php_module_not_found = true;
                                                } else {
                                                    echo '<span class="label label-success">' . $module . '</span> ';
                                                }
                                            }
                                            
                                            if ($php_module_not_found) {
                                                echo '<br/><br/>';
                                                echo '<div class="alert alert-danger" role="alert">' . 'You have some missing modules that are required' . '</div>';
                                            }
                                            
                                            ?>
                                        </div>
                                    </div>
				
					<section id="installation">
						<legend>Installation</legend>
						<ol>
							<li>Download the latest version of Hotaru CMS.</li>
							<li>Create a database called 'hotaru' in you MySQL (using phpMyAdmin or similar).</li>
							<li>Rename '/config/settings_default.php' to '/config/settings.php'.</li>
							<li>Open '/config/settings.php' and edit the top section with your database details and path to Hotaru, e.g. 'http://example.com/'</li>
							<li>Upload the contents of the 'hotaru' folder to your server.</li>
							<li>Files should have permissions set to 644 and folders should be set to 755, except...</li>
							<li>Set '/cache' and its sub-folders to 777</li>
							<li>Go to 'http://example.com/hotaru/install/install.php' and follow the steps</li>
							<li>When finished, delete the 'install' folder.</li>
						</ol>
					</section>
					
					<section id="settings">
						<legend>Setting up your site</legend>
						<ol>
							<li>Log into Admin and go to Admin -> Settings</li>
							<li>Change the settings as appropriate, but leave 'DB_CACHE' off for now.</li>
							<li>Download plugins from the [Plugin Downloads forum][3], unzip and upload them to the '/content/plugins/' directory.</li>
							<li>Go to Admin -> Plugin Management and install the plugins one by one.</li>
							<li>Edit settings for each plugin listed in the sidebar under Plugin Settings.</li>
							<li>Click the site title/banner to view your changes.</li>
							<li>When finished, return to Admin -> Settings and set 'DB_CACHE' to true.</li>
						</ol>
					</section>
					
					<section id="upgrading">
						<legend>Upgrading</legend>
						<ol>
							<li>Backup your database.</li>
							<li>Download the latest version of Hotaru CMS.</li>
							<li>Turn off all your plugins.</li>
							<li>Overwrite ALL the old files. If you've made any customizations, read up on the [Hotaru File Organization][2]</li>
							<li>Go to /install/upgrade.php and follow the steps</li>
							<li>Turn your plugins back on</li>
							<li>Reactivate your widgets</li>
							<li>When finished, delete the install folder.</li>
						</ol>	
					</section>
			
					<section id="requirements">
						<legend>Requirements</legend>
						<ul>
							<li><a href="http://www.php.net/">PHP</a> version <strong>5.3.2</strong> or higher.</li>
							<li><a href="http://www.mysql.com/">MySQL</a> version <strong>5.0</strong> or higher.</li>
						</ul>	
					</section>
					
					<section id="friendly_URLS">
						<legend>Friendly URLS</legend>
						<p>If you want to use friendly urls, rename <strong>'htaccess_default'</strong> to <strong>'.htaccess'</strong>, and edit it according to the instructions within 
						the htaccess file. Then go to Admin -> Settings and change the *friendly urls* setting to true.</p>
					</section>
					
					<section id="troubleshooting">
						<legend>Troubleshooting</legend>
						<p>If you're having trouble installing Hotaru, please post your questions with as much detail as possible [in the forums][4]. Thanks.</p>
						<ol>
							<li>http://docs.hotarucms.org/index.php/Getting_Started#Installing_and_Upgrading</li>
							<li>http://docs.hotarucms.org/index.php/File_Organization</li>
							<li>http://forums.hotarucms.org/forumdisplay.php?18-Plugin-Downloads</li>
							<li>http://forums.hotarucms.org/forum.php</li>
						<ol> 
					</section>
					<!--<legend>Online Resources</legend>-->

				</div><!-- /.col-md-9 -->
				
				<div class="col-md-3 bs-sidebar">
					<ul class="nav nav-list bs-sidenav">
						<li><a href="#installation"><i class="fa fa-chevron-left"></i> Installation</a></li>
						<li><a href="#settings"><i class="fa fa-chevron-left"></i> Setting up your site</a></li>
						<li><a href="#upgrading"><i class="fa fa-chevron-left"></i> Upgrading</a></li>
						<li><a href="#requirements"><i class="fa fa-chevron-left"></i> Requirements</a></li>
						<li><a href="#friendly_URLS"><i class="fa fa-chevron-left"></i> Friendly URLS</a></li>
						<li><a href="#troubleshooting"><i class="fa fa-chevron-left"></i> Troubleshooting</a></li>
					</ul>
				</div>
			</div><!-- /.row -->
		</div> <!-- /.container -->
	