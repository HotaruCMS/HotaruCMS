		
		<!-- Step Title -->
		<legend><?php echo $lang['install_step4']; ?> </legend>
		
		<div class='alert alert-success' role='alert'>
			<strong><?php echo $lang['install_step4_installation_complete']; ?></strong>
			<!-- Complete Step Progress Bar -->
			<div class='progress'>
				<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width: 100%'>
					<span class='sr-only'>100% Complete</span>
				</div>
			</div>
                        <?php echo $lang['install_step4_installation_delete']; ?>
                        
		</div>

		<!-- Step content -->
		<?php
                
                $plugman = Libs\PluginManagement::instance();
                $pluginArray = $plugman->getPluginsArray($h);
                
                $recommend1 = array('bookmarking', 'user_signin', 'widgets', 'users', 'submit', 'comments', 'categories', 'vote', 'stop_spam', 'recaptcha');
                $recommend2 = array('gravatar', 'category_manager', 'search', 'tags', 'post_manager', 'user_manager', 'comment_manager', 'akismet', 'related_posts');
                
                echo '<div class="panel panel-default">';
                echo '<div class="panel-heading">Available Plugins</div>';
                echo '<div class="panel-body">';

                if ($pluginArray) {
                    foreach ($pluginArray as $plugin) {
                        if (in_array($plugin, $recommend1)) {
                            $label = '<span class="label label-success">' . $plugin . '</span>';
                        } elseif (in_array($plugin, $recommend2)) {
                            $label = '<span class="label label-primary">' . $plugin . '</span>';
                        } else {
                            $label = $plugin;
                        }
                        
                        print "<div class='col-md-3'>"
                            . $label
                            . " <span class='plugin-status' id=plugin-" . $plugin  . "></span>"
                        . " </div>";
                    }
                } else {
                    echo 'You have no plugins available in the plugins folder';
                }

                echo '</div>';
                echo '</div>';
                
                ?>
                
                <div class='row'>
                    <div class='col-md-12'>
                        <input class="btn btn-success btn-sm" type="button" onclick="installPlugins(1)" value="Install Top Recommended Plugins">&nbsp;&nbsp;
                        <input class="btn btn-primary btn-sm" type="button" onclick="installPlugins(2)" value="Install Secondary Recommended Plugins">&nbsp;&nbsp;    
                    </div>
                </div>
                
                
                <hr>
		<p class='text-success'><?php echo $lang['install_step4_installation_go_play']; ?></p>

                <?php
			if ($phpinfo) {
				$h->showMessages();        
			} else {
		?>	
			<form role='form' name='install_admin_reg_form' action='index.php?step=4' method='post'>    
				<input type='hidden' name='phpinfo' value='true' />
				<input type='hidden' name='step' value='4' />
				<input class='update btn btn-default' type='submit' value='<?php echo $lang['install_step4_form_check_php']; ?>' />
                                <br/>
			</form>
                        <br/>
		<?php } ?>
                
		<div class='form-actions'>
			<!-- Previous/Next buttons -->
			<a href='index.php?step=3' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
			<a href='<?php echo BASEURL; ?>index.php' class='btn btn-success pull-right' role='button'><?php echo $lang['install_home']; ?> <i class='fa fa-arrow-right'></i></a>
		</div>
                        
                        
                        
                        
                        
<script type='text/javascript'>
function installPlugins(type) {  

        jArray1 = <?php echo json_encode($recommend1); ?>;
        jArray2 = <?php echo json_encode($recommend2); ?>;
        
        if (type === 1) {
            jArray = jArray1;
        } else {
            jArray = jArray2;
        }
        
        $('.plugin-status').html('&nbsp;');
        
        var def = [];
        $.each(jArray, function( index, value ) {
            // have prepareLayer return a _promise_ to return
            def.push(installPlugin(value));
        });
        
        // use "when" to call "postPreparation" once every
        // promise has been resolved
        $.when.apply($, def).done(postPreparation);
}
        
      
        
function installPlugin(folder) {

        var sendurl = "<?php echo SITEURL; ?>admin_index.php";
        var formdata = "page=plugin_management&action=install&ajax=true&plugin="+folder;
        
        var dfd=$.Deferred();
        
        $.ajax(
            {
            type: 'get',
                    url: sendurl,
                    cache: false,
                    data: formdata,
                    beforeSend: function () {
                                    $('#plugin-' + folder).html('<i class="fa fa-spinner fa-spin"></i>');
                            },
                    error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                    $('#plugin-' + folder).html('<i class="fa fa-warning" style="color:red;"></i>').fadeIn("fast");
                                    
                                    //$('#adminNews').removeClass('power_on').addClass('warning_on');
                                    dfd.resolve();
                    },
                    success: function(data) { // success means it returned some form of json code to us. may be code with custom error msg                                                                               
                                    if (data === 1) {
                                        $('#plugin-' + folder).html('<i class="fa fa-check" style="color:green;"></i>').fadeIn("slow");
                                    } else {
                                        $('#plugin-' + folder).html('<i class="fa fa-warning" style="color:red;"></i>').fadeIn("fast");
                                    }
                                    dfd.resolve();
                    },
                    dataType: "json"
    });
    return dfd.promise();
};

function postPreparation()
{
        // Re-sort all orders and remove any accidental gaps
        refreshPluginOrder();
        
         
        // turn on right arrow next
        //$('#update-step3-right').removeClass('disabled');
}
        
function refreshPluginOrder() {

        var sendurl = "<?php echo SITEURL; ?>admin_index.php";
        var formdata = "page=plugin_management&action=refreshOrder";
        
        $.ajax(
            {
            type: 'get',
                    url: sendurl,
                    cache: false,
                    data: formdata,
                    beforeSend: function () {
                                    
                            },
                    error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                    //$('#plugin-' + folder).html('<i class="fa fa-warning"></i>').fadeIn("fast");
                                    
                                    //$('#adminNews').removeClass('power_on').addClass('warning_on');
                    },
                    success: function(data) { // success means it returned some form of json code to us. may be code with custom error msg                                                                               
                                    
                                    //$('#hotaruImg').fadeOut("slow");
                                     
                    },
                    dataType: "json"
    });
        
};
  
  
        // systemInfo feedback
function sendFeedback() {
        var sendurl = "<?php echo SITEURL; ?>admin_index.php?page=systeminfo_feedback";
        
        $.ajax(
            {
            type: 'get',
                    url: sendurl,
                    cache: false,
                    //data: formdata,
                    beforeSend: function () {
                                    //$('#adminNews').html('<img src="' + SITEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>&nbsp;Loading latest news.<br/>');
                            },
                    error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                    //$('#adminNews').html('ERROR');
                                    //$('#adminNews').removeClass('power_on').addClass('warning_on');
                    },
                    success: function(data) { // success means it returned some form of json code to us. may be code with custom error msg                                                                               
                                    //$('#adminNews').html(data).fadeIn("fast");
                                    //$('#hotaruImg').fadeOut("slow");
                                     
                    },
                    dataType: "html"
    });
};
</script>