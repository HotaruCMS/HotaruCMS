<?php
    require_once(PLUGINS . 'autoreader/autoreader.php');
    $arSettings = new Autoreader($h);
    $campaigns = $arSettings->getCampaigns($h);

    $action = $h->cage->post->testAlnumLines('action');


     switch ($action) {
        case "fetch":            
            $fetched= $arSettings->adminForcefetch($h);
            $array = array('fetched'=> $fetched);
            echo json_encode($array);
            exit;
        case "delete":
            $result = $arSettings->adminDelete($h);
            echo $result;
            exit;
        case "reset":
            $result = $arSettings->adminReset($h);
            echo $result;
            exit;
     default :
         //print "default";

    }

 ?>

  <div class="wrap">
    <h2>Campaigns</h2>     
  
    <table class="widefat"> 
      <thead>
        <tr>
          <th scope="col" style="text-align: center">ID</th>
          <th scope="col">Title</th>
          <th style="text-align: center" scope="col">Active</th>
      	  <th style="text-align: center" scope="col">Total Posts</th>
      	  <th scope="col">Last Active</th>
      	  <th scope="col" colspan="4" style="text-align: center">Actions</th>
        </tr>
      </thead>
      
      <tbody id="the-list">            
        <?php if(!$campaigns): ?>
          <tr> 
            <td colspan="5">No campaigns to display</td> 
          </tr>  
        <?php else: ?>     
          <?php $class = ''; ?>  
          
          <?php foreach($campaigns as $campaign): ?>
          <?php $class = ('alternate' == $class) ? '' : 'alternate'; ?>             
          <tr id='campaign-<?php echo $campaign->id ?>' class='<?php echo $class ?> <?php if($h->cage->get->getInt('id') == $campaign->id) echo 'highlight'; ?>'>
            <th scope="row" style="text-align: center"><?php echo $campaign->id ?></th> 
            <td><?php echo $campaign->title; ?></td>
            <td style="text-align: center"><?php if ($campaign->active) {echo 'Yes'; } else {echo 'No';}  ?></td>
            <td style="text-align: center"><?php echo $campaign->count ?></td>        
            <td><?php echo $campaign->lastactive?></td>
            <td><?php echo "<a id='edit_" . $campaign->id . "' href='#' class='edit'>Edit</a></td>"; ?>
            <td><?php echo "<a id='fetch_" . $campaign->id . "' href='#' class='fetch' >" .'Fetch' . "</a>"; ?></td>
            <td><?php echo "<a id='reset_" . $campaign->id . "' href='#' class='reset' >" .'Reset' . "</a>"; ?></td>
            <td><a href="#" id="delete_<?php echo $campaign->id; ?>" class="delete">Delete</a></td>
          </tr>              
          <?php endforeach; ?>                    
        <?php endif; ?>
      </tbody>
    </table>
           
  </div>

 <script type='text/javascript'>
    jQuery('document').ready(function($) {

        $('.delete').click(function(){
            var answer = confirm('You are about to delete the campaign. This action does not remove campaign generated posts. Delete ? '+jQuery(this).attr('title'));            
            if (answer) {
                var campign_ref = $(this).attr('id').split('_');
                var campaign_id = campign_ref[campign_ref.length-1];
                var formdata = 'action=delete&s=delete&id=' + campaign_id;
                var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=autoreader_list';

                 $.ajax(
                    {
                    type: 'post',
                    url: sendurl,
                    data: formdata,
                    error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                    $('#delete_' + campaign_id).html('ERR');
                    },
                    success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                            if (data.error === true) {
                            }
                            else
                            {
                                $('#delete_' + campaign_id).parent().parent().delay(1000).addClass('red').fadeOut(2000);
                            }
                    },
                    dataType: "json"
                });
            }
            return false;
            });

         $('.reset').click(function(){
            var answer = confirm('Are you sure you want to reset this campaign? Resetting does not affect already created posts ? '+jQuery(this).attr('title'));
            if (answer) {
            var campign_ref = $(this).attr('id').split('_');
                var campaign_id = campign_ref[campign_ref.length-1];
                var formdata = 'action=reset&s=reset&id=' + campaign_id;
                var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=autoreader_list';

                 $.ajax(
                    {
                    type: 'post',
                    url: sendurl,
                    data: formdata,
                    error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                    $('#reset_' + campaign_id).html('ERR');
                    },
                    success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                            if (data.error === true) {
                            }
                            else
                            {
                                $('#reset_' + campaign_id).parent().parent().addClass('yellow');
                            }
                    },
                    dataType: "json"
                });
            }
            return false;
            });

        $(".fetch").click(function() {
         var answer = confirm('Are you sure you want to process all feeds from this campaign? '+jQuery(this).attr('title'));
            if (answer) {

            var campign_ref = $(this).attr('id').split('_');
            var campaign_id = campign_ref[campign_ref.length-1];
            var formdata = 'action=fetch&s=forcefetch&id=' + campaign_id;
            var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=autoreader_list';

            $.ajax(
                {
                type: 'post',
                url: sendurl,
                data: formdata,
                beforeSend: function () {
                                $('#fetch_' + campaign_id).html('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>');
                        },
                error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                $('#fetch_' + campaign_id).html('ERR');
                },
                success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                        if (data.error === true) {
                        }
                        else
                        {
                            $('#fetch_' + campaign_id).html(data.fetched);
                        }
                },
                dataType: "json"
            });
        }
        return false;
       });


       $(".edit").click(function(event) {
        event.preventDefault();       
        var campign_ref = $(this).attr('id').split('_');
        var campaign_id = campign_ref[campign_ref.length-1];        
        var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=autoreader_add&action=edit&id=' + campaign_id;

        $("#admin_plugin_content")
                .fadeOut("fast")
                .text("... loading ...")
                .load(sendurl)
                .fadeIn("fast");


        return false;
       });

        $(".delete").click(function(event) {
        event.preventDefault();
        var campign_ref = $(this).attr('id').split('_');
        var campaign_id = campign_ref[campign_ref.length-1];
        var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=autoreader_list&action=delete&id=' + campaign_id;

        return false;
       });



     });
 </script>
  
