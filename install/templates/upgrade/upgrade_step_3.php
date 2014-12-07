<!-- Step title -->
<legend><?php echo $lang['upgrade_step_plugins']; ?></legend>

<p class='text-primary'>
    <strong><i class='fa fa-check'></i> <?php echo $lang['upgrade_step_plugins_details']; ?></strong>
</p>
<!-- Complete Step Progress Bar -->
<div class='progress'>
        <div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='75' aria-valuemin='0' aria-valuemax='100' style='width: 75%'>
                <span class='sr-only'>75% Complete</span>
        </div>
</div>

<!-- Step content -->

<?php
        // uninstall and reinstall all plugins that are set
        $plugman = new \Libs\PluginManagement();
        
        $plugins = array_keys($h->plugins['activeFolders']);
        echo '<br/>';
        echo '<div class="panel panel-primary">';
        echo '<div class="panel-heading">Installed Plugins</div>';
        echo '<div class="panel-body">';
        
        if (isset($h->plugins['activeFolders'])) {
            foreach ($h->plugins['activeFolders'] as $key => $plugin) {
                print "<div class='col-md-3'><span class='plugin-status' id=plugin-" . $key  . "></span> " . $key . " </div>";
            }
        } else {
            echo 'You have no plugins installed';
        }
        
        echo '</div>';
        echo '</div>';
        
        //echo 'You ' . plugins not installed
        
        if (isset($h->plugins['activeFolders'])) {
            echo '<div class="text-center">';
                echo '<input class="btn btn-success" type="button" onclick="updatePlugins()" value="Refresh All Plugins">';
            echo '</div>';
        }
        
        echo '<br/><br/>';
?>

<?php $h->showMessages(); ?>

        <div class='form-actions'>
                <!-- Previous/Next buttons -->
                <a href='index.php?step=2&action=upgrade' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
                <a href='index.php?step=4&action=upgrade' id="update-step3-right" class='btn btn-default pull-right disabled' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
        </div>




<script type='text/javascript'>
function updatePlugins() {  

        var jArray = <?php echo json_encode($plugins ); ?>
        
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
        var formdata = "page=plugin_management&action=reactivateAjax&ajax=true&plugin="+folder;
        
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
        //refreshPluginOrder();

        // clear all caches one more time including langage cache
         
        // turn on right arrow next
        $('#update-step3-right').removeClass('disabled');
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