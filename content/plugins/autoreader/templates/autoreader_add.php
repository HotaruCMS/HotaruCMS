<?php
    # Dependencies     
    require_once(PLUGINS . 'autoreader/helper/edit.helper.php' );  
    require_once(PLUGINS . 'autoreader/autoreader.php');
    $arSettings = new Autoreader($h);
     
    $id = 0;
    $action = $h->cage->post->testAlnumLines('action');  
    $action_get = $h->cage->get->testAlnumLines('action');
    if (!$action) { $action = $action_get; }  
    switch ($action) {
        case "edit":               
            $data = $arSettings->adminEdit($h);           
            echo '<h2>Editing Campaign #' . $data['main']['id'] . ", " .  $data['main']['title'] . '</h2>';
            action_add($h, $arSettings,  $data, 'edit');         
            break;
        case "save" :           
            $arSettings->adminCampaignRequest($h);
            if ( $h->cage->post->keyExists('campaign_edit') ) {
                $cid = $h->cage->post->getInt('campaign_edit');                
                $arSettings->adminProcessEdit($h,$cid);
            }
            else {               
                $arSettings->adminProcessAdd($h);
            };
            exit;  // this is an ajax return call, so we don't want any html echoing to the screen
        case "test_feed" :           
            $data = $h->cage->post->testUri('url');            
            $arSettings->adminTestfeed($data);
            exit;  // this is an ajax return call, so we don't want any html echoing to the screen
        default :
            echo '<h2>Add New Campaign</h2>';           
            action_add($h, $arSettings);
    }
   ?>

<div class="wrap">
   
 <?php
function action_add($h, $arSettings, $data=null, $action = 'add') {
    $arSettings = new Autoreader($h);
    $autoreader_settings = $arSettings->getOptionSettings($h);
 ?>
    <form id="edit_campaign" action="" method="post" accept-charset="utf-8">
      
      <?php 
      
      if ($action=='edit') {
            $id =  $data['main']['id'];
            echo input_hidden_tag('campaign_edit', $id);
            }
      else {
            echo input_hidden_tag('campaign_add', 1);
            $data = $arSettings->campaign_structure;
            }
      ?>

      <ul id="edit_buttons" class="submit">
         <?php if ($autoreader_settings['wpo_help']) { ?>
                <li><a href="help.php?item=campaigns" class="help_link">Help</a></li>
         <?php } ?>       
        <li><input type="submit" name="edit_submit" value="Submit" id="edit_submit" /></li>
      </ul>

      <div id="admin_plugin_tabs">
      <ul class="tabs">
        <li class="current"><a href="#section_basic" id="tab_basic">Basic</a></li>
        <li><a href="#section_feeds" id="tab_feeds">Feeds</a></li>
        <li><a href="#section_categories" id="tab_categories">Categories</a></li>
        <li><a href="#section_rewrite" id="tab_rewrite">Rewrite</a></li>
        <li><a href="#section_options" id="tab_options">Options</a></li>
        <?php if($action == 'edit'): ?>
            <li><a href="#section_tools" id="tab_tools">Tools</a></li>
        <?php endif ?>
      </ul>
      </div>


      <div id="edit_sections">
        <!-- Basic section -->
        <div class="section current" id="section_basic">
          <div class="longtext required">
            <?php echo label_for('campaign_title', 'Title') ?>
            <?php echo input_tag('campaign_title', _data_value($data['main'], 'title')) ?>
            <p class="note">Tip: pick a name that describes the collection of this campaign's feeds (eg: Basketball)</p>
          </div>

          <div class="checkbox required">
            <?php echo label_for('campaign_active', 'Active?') ?>
            <?php echo checkbox_tag('campaign_active', 1, _data_value($data['main'], 'active', true)) ?>
            <p class="note">If inactive, the parser will ignore these feeds</p>
          </div>

          <div class="text">
            <?php echo label_for('campaign_slug', 'Campaign slug') ?>
            <?php echo input_tag('campaign_slug', _data_value($data['main'], 'slug')) ?>
            <p class="note">Optionally, you can set an identifier for this campaign. Useful for detailed track of your ad-revenue.</p>
          </div>
        </div>

        <!-- Feeds section -->
        <div class="section" id="section_feeds">
          <p>Please fill in at least one feed. If you\'re not sure about the exact feed url, just type in the domain name, and the feed will be autodetected</p>

          <div id="edit_feed">
            <?php if(isset($data['feeds']['edit'])): ?>
              <?php foreach($data['feeds']['edit'] as $id => $feed): ?>
              <div class="inlinetext required">
                <?php echo label_for('campaign_feed_edit_' . $id, 'Feed URL') ?>
                <?php echo input_tag('campaign_feed[edit]['. $id .']', $feed, 'disabled=disabled class=input_text id=campaign_feed_edit_' . $id) ?>
                <?php echo checkbox_tag('campaign_feed[delete]['.$id.']', 1, (isset($data['feeds']['delete']) && _data_value($data['feeds']['delete'], $id)), 'id=campaign_feed_delete_' . $id) ?> <label for="campaign_feed_delete_<?php echo $id ?>" class="delete_label">Delete ?</label>
              </div>
              <?php endforeach ?>
            <?php endif ?>

            <?php if(isset($data['feeds']['new'])): ?>
              <?php foreach($data['feeds']['new'] as $i => $feed): ?>
              <div class="inlinetext required">
                <?php echo label_for('campaign_feed_new_' . $i, 'Feed URL') ?>
                <?php echo input_tag('campaign_feed[new]['.$i.']', $feed, 'class=input_text id=campaign_feed_new_' . $i) ?>
              </div>
              <?php endforeach ?>
            <?php else: ?>
              <?php for($i = 0; $i < 4; $i++): ?>
              <div class="inlinetext required">
                <?php echo label_for('campaign_feed_new_' . $i, 'Feed URL') ?>
                <?php echo input_tag('campaign_feed[new][]', null, 'class=input_text id=campaign_feed_new_' . $i) ?>
              </div>
              <?php endfor ?>
            <?php endif ?>
          </div>
         <?php if ($autoreader_settings['wpo_premium']) { ?>
            <a href="#add_feed" id="add_feed">Add more</a> | <a href="#" id="test_feeds">Check all</a>
          <?php } ?>
        </div>

        <!-- Categories section -->
        <div class="section" id="section_categories">
          <p>These are the categories where the posts will be created once they're fetched from the feeds.</p>
          <p>Please select one.</p>

          <ul id="categories">
            <?php $arSettings->adminEditCategories($h, $data) ?>
          </ul>

           <?php if ($autoreader_settings['wpo_premium']) { ?>
                <a href="#quick_add" id="quick_add">Quick add</a>
           <?php } ?>
        </div>

        <!-- Rewrite section -->
        <div class="section" id="section_rewrite">
          <p>Want to transform a word into another? Or link a specific word to some website?
<?php // echo '<a href=' . $arSettings->helpurl . '" class="help_link">Read more</a>' ?></p>

          <ul id="edit_words">
            <?php if(isset($data['rewrites']) && count($data['rewrites'])): ?>
              <?php foreach($data['rewrites'] as $i => $rewrite): ?>
                <li class="word">
                  <div class="origin textarea">
                    <?php echo label_for('campaign_word_origin_' . $i, 'Origin') ?>
                    <?php echo textarea_tag('campaign_word_origin['.$i . ']', $rewrite['origin']['search'], 'id=campaign_word_origin_' . $rewrite->id) ?>
                    <label class="regex">
                      <?php echo checkbox_tag('campaign_word_option_regex['. $i .']', 1, $rewrite['origin']['regex']) ?>
                      <span><?php _e('RegEx', 'wpomatic') ?></span>
                    </label>
                  </div>

                  <div class="rewrite textarea">
                    <label>
                      <?php echo checkbox_tag('campaign_word_option_rewrite['. $i .']', 1, isset($rewrite['rewrite'])) ?>
                      <span>Rewrite to:</span>
                    </label>
                    <?php echo textarea_tag('campaign_word_rewrite['. $i .']', _data_value($rewrite, 'rewrite')) ?>
                  </div>

                  <div class="relink textarea">
                    <label>
                      <?php echo checkbox_tag('campaign_word_option_relink['. $i .']', 1, isset($rewrite['relink'])) ?>
                      <span>Relink to:</span>
                    </label>
                    <?php echo textarea_tag('campaign_word_relink['. $i .']', _data_value($rewrite, 'relink')) ?>
                  </div>
                </li>
              <?php endforeach ?>
            <?php else: ?>
            <li class="word">
              <div class="origin textarea">
                <label for="campaign_word_origin_new1">Origin</label>
                <textarea name="campaign_word_origin[new1]" id="campaign_word_origin_new1"></textarea>
                <label class="regex"><input type="checkbox" name="campaign_word_option_regex[new1]" /> <span>RegEx</span></label>
              </div>
              <div class="rewrite textarea">
                <label><input type="checkbox" value="1" name="campaign_word_option_rewrite[new1]" /> <span>Rewrite to:</span></label>
                <textarea name="campaign_word_rewrite[new1]"></textarea>
              </div>
              <div class="relink textarea">
                <label><input type="checkbox" value="1" name="campaign_word_option_relink[new1]" /> <span>Relink to:</span></label>
                <textarea name="campaign_word_relink[new1]"></textarea>
              </div>
            </li>
            <?php endif ?>
          </ul>

           <?php if ($autoreader_settings['wpo_premium']) { ?>
                <a href="#add_word" id="add_word">Add more</a>
           <?php } ?>
        </div>

        <!-- Options -->
        <div class="section" id="section_options">
          <?php if(isset($campaign_edit)): ?>
          <div class="section_warn">
            <img src="<?php echo $arSettings->tplpath ?>/images/icon_alert.gif" alt="Warning" class="icon" />
            <h3>Remember that</h3>
            <p>Changing these options only affects the creation of posts after the next time feeds are parsed.</p>
            <p>If you need to edit existing posts, you can do so by using the options under the Tools tab</p>
          </div>
          <?php endif ?>

             <?php if ($autoreader_settings['wpo_premium']) { ?>
                  <div class="checkbox">
                    <label for="campaign_templatechk">Custom post template</label>
                    <?php echo checkbox_tag('campaign_templatechk', 1, _data_value($data['main'], 'template')) ?>

                    <div id="post_template" class="textarea <?php if(_data_value($data['main'], 'template', '{content}') !== '{content}') echo 'current' ?>">
                      <?php echo textarea_tag('campaign_template', _data_value($data['main'], 'template', '{content}')) ?>
                      <a href="#" id="enlarge_link">Enlarge</a>

                      <p class="note" id="tags_note">
                        'Valid tags:
                      </p>
                      <p id="tags_list">
                        <span class="tag">{content}</span>, <span class="tag">{title}</span>, <span class="tag">{permalink}</span>, <span class="tag">{feedurl}</span>, <span class="tag">{feedtitle}</span>, <span class="tag">{feedlogo}</span>,<br /> <span class="tag">{campaigntitle}</span>, <span class="tag">{campaignid}</span>, <span class="tag">{campaignslug}</span>
                      </p>
                    </div>

                    <p class="note">Read about <a href="<?php echo $arSettings->helpurl; ?>" class="help_link">post templates</a>, or check some <a href="<?php echo $arSettings->helpurl; ?>" class="help_link">examples</a> ?></p>
                  </div>
          <?php } ?>
          <div class="multipletext">
            <?php
              $f = _data_value($data['main'], 'frequency');

              if($f) {
                $frequency = WPOTools::calcTime($f);
              }
              else
                $frequency = array();
            ?>

            <label>Frequency</label>

             <?php if ($autoreader_settings['wpo_premium']) { ?>
                <?php echo input_tag('campaign_frequency_d', _data_value($frequency, 'days', 1), 'size=2 maxlength=3')?>
                d

                <?php echo input_tag('campaign_frequency_h', _data_value($frequency, 'hours', 5), 'size=2 maxlength=2')?>
                h

                <?php echo input_tag('campaign_frequency_m', _data_value($frequency, 'minutes', 0), 'size=2 maxlength=2')?>
                m
             <?php } ?>

             <select name="campaign_frequency_h">
              <option value="1">hourly</option>
              <option value="12">twice daily</option>
              <option value="24">daily</option>
              <option value="84">weekly</option>
            </select>

            <p class="note">How often should feeds be checked? </p>
          </div>

          <?php if ($autoreader_settings['wpo_premium']) { ?>
              <div class="checkbox">
                <?php echo label_for('campaign_cacheimages', 'Cache images') ?>
                <? // need to install timthumb folder to get the following working  ?>
                <?php  echo checkbox_tag('campaign_cacheimages', 1, _data_value($data['main'], 'cacheimages', is_writable($arSettings->cachepath))) ?>
                <p class="note">Images will be stored in your server, instead of hotlinking from the original site.
                    <a href="helpurl image_caching" class="help_link">More</a></p>
              </div>
          <?php } ?>

          <div class="checkbox">
            <?php echo label_for('campaign_feeddate', 'Use feed date') ?>
            <?php echo checkbox_tag('campaign_feeddate', 1, _data_value($data['main'], 'feeddate', false)) ?>
            <p class="note">Use the original date from the post instead of the time the post is created by this plugin.
                </p>
          </div>

          <?php if ($autoreader_settings['wpo_premium']) { ?>
              <div class="checkbox">
                <?php echo label_for('campaign_dopingbacks', 'Perform pingbacks') ?>
                <?php echo checkbox_tag('campaign_dopingbacks', 1, _data_value($data['main'], 'dopingbacks', false)) ?>
              </div>
          <?php } ?>

          <div class="radio">
            <label class="main">Type of post to create</label>

            <?php echo radiobutton_tag('campaign_posttype', 'new', !isset($data['main']['posttype']) || _data_value($data['main'], 'posttype') == 'new', 'id=type_new') ?>
            <?php echo label_for('type_new', 'Published (New)') ?>

            <?php echo radiobutton_tag('campaign_posttype', 'pending', _data_value($data['main'], 'posttype') == 'pending', 'id=type_pending') ?>
            <?php echo label_for('type_pending', 'Pending') ?>
          </div>

          <div class="text">
            <?php echo label_for('campaign_author', 'Author:') ?>
 <?php // echo select_tag('campaign_author', options_for_select($author_usernames, _data_value($data['main'], 'author', 'admin'))) ?>
            <p class="note">The created posts will be assigned to admin.</p>
          </div>

          <div class="text required">
            <?php echo label_for('campaign_max', 'Max items to create on each fetch') ?>
            <?php echo input_tag('campaign_max', _data_value($data['main'], 'max', '10'), 'size=2 maxlength=3') ?>
            <p class="note">Set it to 0 for unlimited. If set to a value, only the last X items will be selected, ignoring the older ones.</p>
          </div>

          <div class="checkbox">
            <?php echo label_for('campaign_linktosource', 'Post title links to source?') ?>
            <?php echo checkbox_tag('campaign_linktosource', 1, _data_value($data['main'], 'linktosource', false)) ?>
          </div>

          <?php if ($autoreader_settings['wpo_premium']) { ?>
              <div class="radio">
                <label class="main">Discussion options:</label>

                <?php echo select_tag('campaign_commentstatus',
                            options_for_select(
                              array('open' => 'Open',
                                    'closed' => 'Closed',
                                    'registered_only' => 'Registered only'
                                    ), _data_value($data['main'], 'comment_status', 'open'))) ?>

                <?php echo checkbox_tag('campaign_allowpings', 1, _data_value($data['main'], 'allowpings', true)) ?>
                <?php echo label_for('campaign_allowpings', 'Allow pings') ?>
              </div>
         <?php } ?>
        </div>

         <?php if ($autoreader_settings['wpo_premium']) { ?>
        <?php if($action == 'edit'): ?>
        <!-- Tools -->
        <div class="section" id="section_tools">
          <div class="buttons">
            <h3>Posts action</h3>
            <p class="note">The selected action applies to all the posts created by this campaign</p>

            <ul>
              <li>
                <div class="btn">
                  <input type="submit" name="tool_removeall" value="Remove all" />
                </div>
              </li>
              <li>
                <div class="radio">
                  <label class="main">Change status to:</label>

                  <input type="radio" name="campaign_tool_changetype" value="new" id="changetype_new" checked="checked" /> <label for="changetype_new">New</label>
                  <input type="radio" name="campaign_tool_changetype" value="private" id="changetype_private" /> <label for="changetype_private">Private</label>
                  <input type="radio" name="campaign_tool_changetype" value="draft" id="changetype_draft" /> <label for="changetype_draft">Draft</label>
                  <input type="submit" name="tool_changetype" value="Change" />
                </div>
              </li>
              <li>
                <div class="text">
                  <label for="campaign_tool_changeauthor">Change author username to:</label>
                  <?php echo select_tag('campaign_tool_changeauthor', options_for_select($author_usernames, _data_value($data['main'], 'author', 'admin'))) ?>

                  <input type="submit" name="tool_changeauthor" value="Change" />
                </div>
              </li>
            </ul>
          </div>

          <!--
          <div class="btn">
            <label>Test all feeds</label>
            <input type="button" name="campaign_tool_testall_btn" value="Test" />
            <p class="note">This option creates one draft from each feed you added.</p>
          </div>
          -->
        </div>
        <?php endif; ?>
        <?php } ?>
      </div>

        
    </form>



<?php

      };   ?>

      </div>

 <script type='text/javascript'>
    jQuery('document').ready(function($) {

        //$("#tab_container .tab_content").hide(); //Hide all content
        $("ul.tabs li:first").addClass("active").show(); //Activate first tab
        $(".tab_content:first").show(); //Show first tab content

        //On Click Event
        $("#admin_plugin_tabs ul.tabs li").click(function() {
            //alert('hi');
            $("ul.tabs li").removeClass("active"); //Remove any "active" class
            $(this).addClass("active"); //Add "active" class to selected tab
            $(".section").hide(); //Hide all tab content

            var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
            $(activeTab).show(); //Fade in the active ID content
            return false;
        });
     });

     $("#edit_submit").click(function(event) {
        event.preventDefault();

        // Save via AJAX
        //var item_uid = $('.post').attr('id');
        //   item_uid = item_uid.split('-');
        //   item_uid = item_uid[item_uid.length-1];  // this gets the id of the post from the class that WP adds in, gets last element on post id

        var campaign = $("form#edit_campaign").serialize();
      
       // campaign =  $.URLEncode(campaign);
        var formdata =  campaign + "&action=save";
        var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=autoreader_add';

        $.ajax(
            {
            type: 'post',
            url: sendurl,
            data: formdata,
            beforeSend: function () {
                            $('#edit_buttons').append('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>');                           
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
                        if(data.enabled == 'true') { img_src = "active.png"; } else { img_src = "inactive.png"; }
                        $('#edit_buttons').html('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/' + img_src + '"/>');
                    }
                    //$('#return_message').html(data.message).addClass(data.color);
                    //$('#return_message').html(data.message).addClass('message');
                    //$('#return_message').fadeIn(1000).fadeout(1000);
            },
            dataType: "json"
        });


        return false;
      
      });


function toSlug(str) {
    return str.replace(/\W/g, ' ').replace(/\ +/g, '-').replace(/\-$/g, '').replace(/^\-/g, '').toLowerCase();
  }


    // Basic tab
		$('#campaign_title').keyup(function() {
           // alert($('#campaign_title').val().replace(/ /g,'_'));
            $('#campaign_slug').val(toSlug( $('#campaign_title').val()));
		});


    // Feeds tab

		//- Test feed links
		function check_feed(feed) {
		  feed.addClass('input_text');
          if(feed.val().length > 0)
          {
              var formdata = "url=" + feed.val()+ "&action=test_feed";
              var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=autoreader_add';

              $.ajax(
                {
                type: 'post',
                url: sendurl,
                data: formdata,
                beforeSend: function () {
                                feed.addClass('green');
                        },
                error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                feed.addClass('red');
                                 feed.val("Error occured");
                },
                success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                        if (data.result === 'fail') {
                             feed.addClass('red');
                             feed.val(data.error);
                        }
                        else
                        {
                            feed.addClass('green');
                            feed.val(data.url);
                        }
                },
                dataType: "json"
            });
          };         
          feed.addClass('load input_text');
		};


    function update_feeds() {          
          $('#edit_feed div input[type=text]').focus(function() {
             $(this).addClass('input_text').addClass('red');              
          });

          $('#edit_feed div input[type=text]').blur(function() {
            check_feed($(this));
             $(this).removeClass('red');
            //alert('leave');
          });
        };

    update_feeds();




</script>



