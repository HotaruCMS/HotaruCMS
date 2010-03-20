<?php
    require_once(PLUGINS . 'autoreader/autoreader.php');
    $arSettings = new Autoreader($h);
   
    $action = $h->cage->post->testAlnumLines('action');

     switch ($action) {
        case "save":            
            $autoreader_settings = $arSettings->getOptionSettings($h);
            echo json_encode($autoreader_settings);
            exit;
        case "flush":
            $hook = "autoreader_runcron";            
            $cron_data = array('hook'=>$hook);
            $h->pluginHook('cron_flush_hook', 'cron', $cron_data);
            exit;
        default :
            $autoreader_settings = $arSettings->getOptionSettings($h);

    }

 ?>

      
  <div class="wrap">
    <h2>Options</h2>

    <?php if(isset($updated)): ?>
      <div id="added-warning" class="updated"><p><?php echo 'Options saved.'; ?></p></div>
    <?php endif ?>

    <?php if(isset($not_writable)): ?>
      <div class="error"><p><?php echo 'Image cache path ' . WPODIR . $autoreader_settings['wpo_cachepath'] . ' is not writable!'; ?></p></div>
    <?php endif ?>

    <form id="form_options" action="" method="post" accept-charset="utf-8">
      <input type="hidden" name="update" value="1" />

      <ul id="options">
        <li id="options_cron">
             <?php if ($autoreader_settings['wpo_premium']) { ?>
                  <?php echo label_for('option_unixcron', 'Unix cron') ?>
                  <?php echo checkbox_tag('option_unixcron', 1, $autoreader_settings['wpo_unixcron']) ?>
            <?php } ?>

          <h3>Cron command:</h3>
          <div id="cron_command" class="command"><?php echo $arSettings->cron_command ?></div>

           <?php if ($autoreader_settings['wpo_premium']) { ?>
              <h3>WebCron-ready URL:</h3>
              <div id="cron_command" class="command"><?php echo $arSettings->cron_url ?></div>
           <?php } ?>

          <p class="note"><?php echo 'Cron is set up to handle fetching if you are familiar with manually setting cron jobs on your server.'; ?> <?php if ($autoreader_settings['wpo_help']) { ?><a href="<?php echo $arSettings->helpurl ?>cron" class="help_link"><?php echo 'More'; ?></a><?php } ?></p>
          <p class="note">For those not familiar with cron jobs or for shared servers where you cannot access them, you may install the "cron" plugin for hotaru which emulates the function of server cron jobs. This is recommended for most users.</p>
        </li>

        <li>
          <?php echo label_for('option_logging', 'Enable logging') ?>
          <?php echo checkbox_tag('option_logging', 1,$autoreader_settings['wpo_log']) ?>

          <p class="note"><?php echo 'Enable database-driven logging of events.'; ?> <?php if ($autoreader_settings['wpo_help']) { ?><a href="<?php echo $this->helpurl ?>logging" class="help_link"><?php echo 'More'; ?></a><?php } ?></p>
        </li>

        <?php if ($autoreader_settings['wpo_premium']) { ?>
            <li>
              <?php echo label_for('option_logging_stdout', 'Enable logging stdout') ?>
              <?php echo checkbox_tag('option_logging_stdout', 1, $autoreader_settings['wpo_log_stdout']) ?>

              <p class="note"><?php echo 'With this option enabled, Autoreader will attempt to show you logs creation in real time when manual fetching is used.'; ?> <a href="<?php echo $this->helpurl ?>logging" class="help_link"><?php echo 'More'; ?></a></p>
            </li>
        <?php } ?>
            
            <li>
              <?php echo label_for('option_caching','Cache images') ?>
              <?php echo checkbox_tag('option_caching', 1,$autoreader_settings['wpo_cacheimages']) ?>

              <p class="note"><?php echo 'This option overrides any campaign specific image settings'; ?> <?php if ($autoreader_settings['wpo_premium']) { ?><a href="<?php echo $this->helpurl ?>image_caching" class="help_link"><?php echo 'More'; ?></a><?php } ?></p>
            </li>

            <li>
              <?php echo label_for('option_cachepath','Image cache path') ?>
              <?php echo input_tag('option_cachepath', $autoreader_settings['wpo_cachepath']) ?>

              <p class="note"><?php echo 'The path <span id="cachepath">'. PLUGINS . 'autoreader/<span id="cachepath_input">' . $autoreader_settings['wpo_cachepath'] . '</span></span> must exist, be writable by the server and accessible through browser.'; ?></p>
            </li>
       

            <li>
                <a href='#' id="flush_cron">Clear</a> all cron jobs listed for "autoreader" in the hotaru "cron" plugin.<div id ="flush_cron_message"></div>
            </li>


      </ul>

      <p class="submit">
        <input type="submit" id="edit_submit" value="Save" name="commit">
      </p>
    </form>
  </div>

<script type='text/javascript'>
    jQuery('document').ready(function($) {

        $("#flush_cron").click(function() { 
         var answer = confirm('Are you sure you want to remove all current cron jobs for autoreader from cron plugin? Existing posts submitted will not be lost and campaign data will not be altered.');
            if (answer) {
                var formdata = 'action=flush';
                var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=autoreader_options';

            $.ajax(
                {
                type: 'post',
                url: sendurl,
                data: formdata,
                beforeSend: function () {
                                $('#flush_cron_message').html('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>');
                        },
                error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                $('#flush_cron_message').html('An error occured.');
                },
                success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                        if (data.error === true) {
                        }
                        else
                        {
                            $('#flush_cron_message').html(data.count + ' jobs flushed from the cron.');
                        }
                },
                dataType: "json"
            });
        }
        return false;
       });
       

        $("#edit_submit").click(function(event) {
            event.preventDefault();

            var options = $("form#form_options").serialize();

            var formdata = 'options=' + options + '&action=save';
            var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=autoreader_options';

            $.ajax(
                {
                type: 'post',
                url: sendurl,
                data: formdata,
                beforeSend: function () {                   
                                $('p.submit').append('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>');
                        },
                error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                //widget.html('ERROR');
                },
                success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                        if (data.error === true) {
                        }
                        else
                        {
                            var img_src = "";
                            // get required image based on returned data showing new status
                            if(data.saved == 'true') { img_src = "active.png"; } else { img_src = "inactive.png"; }
                           $('p.submit img').attr('src', BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/' + img_src);
                        }
                        //$('#return_message').html(data.message).addClass(data.color);
                        //$('#return_message').html(data.message).addClass('message');
                        //$('#return_message').fadeIn(1000).fadeout(1000);
                },
                dataType: "json"
            });
         
        });

      });

</script>