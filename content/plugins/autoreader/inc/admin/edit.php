<?php require_once(WPOTPL . '/helper/form.helper.php' ) ?>
<?php require_once(WPOTPL . '/helper/edit.helper.php' ) ?>
<?php $this->adminHeader() ?>
  
  <div class="wrap">  
    <?php if(isset($campaign_add)): ?>
    <h2>Add campaign</h2>
    <?php else: ?>
    <h2>Editing campaign</h2>
    <?php endif;?>
    
    <?php if(isset($this->errno) && $this->errno): ?>
    <div id="edit-warning" class="error">
      <p><strong><?php _e('The following errors have been encountered:', 'wpomatic') ?></strong></p>
      <ul>
        <?php foreach($this->errors as $section => $errs): ?>
          <?php if($errs): ?>
          <li>
            <?php echo ucfirst($section) ?>
            <ul class="errors">
              <?php foreach($errs as $error): ?>
              <li><?php echo $error ?></li>
              <?php endforeach ?>
            </ul>
          </li>
          <?php endif?>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php else: ?>
      <?php if(isset($addedid)): ?>
      <div id="added-warning" class="updated"><p><?php printf(__('Campaign added successfully. <a href="%s">Edit it</a> or <a href="%s">fetch it now</a>', 'wpomatic'), $this->adminurl . '&s=edit&id=' . $addedid, wp_nonce_url($this->adminurl . '&amp;s=forcefetch&amp;id=' . $addedid, 'forcefetch-campaign_' . $addedid)) ?></p></div>
      <?php elseif(isset($this->tool_success)): ?>
      <div id="added-warning" class="updated"><p><?php echo $this->tool_success ?></p></div>
      <?php elseif(isset($edited)): ?>    
      <div id="added-warning" class="updated"><p><?php _e('Campaign edited successfully.', 'wpomatic') ?></p></div>
      <?php endif ?>  
    <?php endif; ?>
                  
    <form id="edit_campaign" action="" method="post" accept-charset="utf-8">
      <?php wp_nonce_field('wpomatic-edit-campaign') ?>

      <?php if(isset($campaign_add)): ?>
        <?php echo input_hidden_tag('campaign_add', 1) ?>
      <?php else: ?>
        <?php echo input_hidden_tag('campaign_edit', $id) ?>
      <?php endif; ?>
                              
      <ul id="edit_buttons" class="submit">                       
        <li><a href="<?php echo $this->helpurl ?>campaigns" class="help_link"><?php _e('Help', 'wpomatic') ?></a></li>                                                                             
        <li><input type="submit" name="edit_submit" value="Submit" id="edit_submit" /></li>
      </ul>
            
      <ul id="edit_tabs">
        <li class="current"><a href="#" id="tab_basic"><?php _e('Basic', 'wpomatic') ?></a></li>
        <li><a href="#" id="tab_feeds"><?php _e('Feeds', 'wpomatic') ?></a></li>
        <li><a href="#" id="tab_categories"><?php _e('Categories', 'wpomatic') ?></a></li>
        <li><a href="#" id="tab_rewrite"><?php _e('Rewrite', 'wpomatic') ?></a></li>
        <li><a href="#" id="tab_options"><?php _e('Options', 'wpomatic') ?></a></li>   
        <?php if(isset($campaign_edit)): ?>        
        <li><a href="#" id="tab_tools"><?php _e('Tools', 'wpomatic') ?></a></li>
        <?php endif ?>
      </ul>                 
      
      <div id="edit_sections">                          
        <!-- Basic section -->
        <div class="section current" id="section_basic">
          <div class="longtext required">
            <?php echo label_for('campaign_title', __('Title', 'wpomatic')) ?>
            <?php echo input_tag('campaign_title', _data_value($data['main'], 'title')) ?>
            <p class="note"><?php _e('Tip: pick a name that is general for all the campaign\'s feeds (eg: Paris Hilton)', 'wpomatic' ) ?></p>
          </div>
            
          <div class="checkbox required">
            <?php echo label_for('campaign_active', __('Active?', 'wpomatic')) ?>
            <?php echo checkbox_tag('campaign_active', 1, _data_value($data['main'], 'active', true)) ?>
            <p class="note"><?php _e('If inactive, the parser will ignore these feeds', 'wpomatic' ) ?></p>
          </div> 
          
          <div class="text">
            <?php echo label_for('campaign_slug', __('Campaign slug', 'wpomatic')) ?>
            <?php echo input_tag('campaign_slug', _data_value($data['main'], 'slug')) ?>
            <p class="note"><?php _e('Optionally, you can set an identifier for this campaign. Useful for detailed track of your ad-revenue.', 'wpomatic' ) ?></p>
          </div>
        </div>
        
        <!-- Feeds section -->
        <div class="section" id="section_feeds">
          <p><?php _e('Please fill in at least one feed. If you\'re not sure about the exact feed url, just type in the domain name, and the feed will be autodetected', 'wpomatic') ?></p>
          
          <div id="edit_feed">
            <?php if(isset($data['feeds']['edit'])): ?>
              <?php foreach($data['feeds']['edit'] as $id => $feed): ?>
              <div class="inlinetext required">
                <?php echo label_for('campaign_feed_edit_' . $id, __('Feed URL', 'wpomatic')) ?>
                <?php echo input_tag('campaign_feed[edit]['. $id .']', $feed, 'disabled=disabled class=input_text id=campaign_feed_edit_' . $id) ?>       
                <?php echo checkbox_tag('campaign_feed[delete]['.$id.']', 1, (isset($data['feeds']['delete']) && _data_value($data['feeds']['delete'], $id)), 'id=campaign_feed_delete_' . $id) ?> <label for="campaign_feed_delete_<?php echo $id ?>" class="delete_label">Delete ?</label>
              </div>
              <?php endforeach ?>
            <?php endif ?>
            
            <?php if(isset($data['feeds']['new'])): ?>                  
              <?php foreach($data['feeds']['new'] as $i => $feed): ?>
              <div class="inlinetext required">
                <?php echo label_for('campaign_feed_new_' . $i, __('Feed URL', 'wpomatic')) ?>
                <?php echo input_tag('campaign_feed[new]['.$i.']', $feed, 'class=input_text id=campaign_feed_new_' . $i) ?>
              </div>                           
              <?php endforeach ?>
            <?php else: ?>
              <?php for($i = 0; $i < 4; $i++): ?>
              <div class="inlinetext required">
                <?php echo label_for('campaign_feed_new_' . $i, __('Feed URL', 'wpomatic')) ?>
                <?php echo input_tag('campaign_feed[new][]', null, 'class=input_text id=campaign_feed_new_' . $i) ?>
              </div>                           
              <?php endfor ?>
            <?php endif ?>
          </div>
          
          <a href="#add_feed" id="add_feed"><?php _e('Add more', 'wpomatic') ?></a> | <a href="#" id="test_feeds"><?php _e('Check all', 'wpomatic') ?></a>
        </div>
         
        <!-- Categories section -->
        <div class="section" id="section_categories"> 
          <p><?php _e('These are the categories where the posts will be created once they\'re fetched from the feeds.', 'wpomatic') ?></p><p><?php _e('You have to select at least one.', 'wpomatic') ?></p>
           
          <ul id="categories">
            <?php $this->adminEditCategories($data) ?>
            
            <?php if(isset($data['categories']['new'])): ?>
              <?php foreach($data['categories']['new'] as $i => $catname): ?>
              <li>
                <?php echo checkbox_tag('campaign_newcat[]', 1, true, 'id=campaign_newcat_' . $i) ?>
                <?php echo input_tag('campaign_newcatname[]', $catname, 'class=input_text id=campaign_newcatname_' . $i) ?>
              </li>
              <?php endforeach ?>
            <?php endif ?>
          </ul>          
          
          <a href="#quick_add" id="quick_add"><?php _e('Quick add', 'wpomatic') ?></a>
        </div>
          
        <!-- Rewrite section -->
        <div class="section" id="section_rewrite">
          <p><?php _e('Want to transform a word into another? Or link a specific word to some website?', 'wpomatic') ?> <?php printf(__('<a href="%s" class="help_link">Read more</a>', 'wpomatic'), $this->helpurl . 'campaign_rewrite') ?></p>  
          
          <ul id="edit_words">
            <?php if(isset($data['rewrites']) && count($data['rewrites'])): ?>   
              <?php foreach($data['rewrites'] as $i => $rewrite): ?>
                <li class="word">
                  <div class="origin textarea">
                    <?php echo label_for('campaign_word_origin_' . $i, __('Origin', 'wpomatic')) ?>
                    <?php echo textarea_tag('campaign_word_origin['.$i . ']', $rewrite['origin']['search'], 'id=campaign_word_origin_' . $rewrite->id) ?>
                    <label class="regex">
                      <?php echo checkbox_tag('campaign_word_option_regex['. $i .']', 1, $rewrite['origin']['regex']) ?>
                      <span><?php _e('RegEx', 'wpomatic') ?></span>
                    </label>
                  </div>               
                  
                  <div class="rewrite textarea">
                    <label>
                      <?php echo checkbox_tag('campaign_word_option_rewrite['. $i .']', 1, isset($rewrite['rewrite'])) ?>
                      <span><?php _e('Rewrite to:', 'wpomatic') ?></span>
                    </label>
                    <?php echo textarea_tag('campaign_word_rewrite['. $i .']', _data_value($rewrite, 'rewrite')) ?>
                  </div>            
                  
                  <div class="relink textarea">
                    <label>
                      <?php echo checkbox_tag('campaign_word_option_relink['. $i .']', 1, isset($rewrite['relink'])) ?>
                      <span><?php _e('Relink to:', 'wpomatic') ?></span>
                    </label>
                    <?php echo textarea_tag('campaign_word_relink['. $i .']', _data_value($rewrite, 'relink')) ?>
                  </div>           
                </li>
              <?php endforeach ?>
            <?php else: ?>
            <li class="word">
              <div class="origin textarea">
                <label for="campaign_word_origin_new1"><?php _e('Origin', 'wpomatic') ?></label>
                <textarea name="campaign_word_origin[new1]" id="campaign_word_origin_new1"></textarea> 
                <label class="regex"><input type="checkbox" name="campaign_word_option_regex[new1]" /> <span><?php _e('RegEx', 'wpomatic') ?></span></label>
              </div>               
              <div class="rewrite textarea">
                <label><input type="checkbox" value="1" name="campaign_word_option_rewrite[new1]" /> <span><?php _e('Rewrite to:', 'wpomatic') ?></span></label>
                <textarea name="campaign_word_rewrite[new1]"></textarea>
              </div>            
              <div class="relink textarea">
                <label><input type="checkbox" value="1" name="campaign_word_option_relink[new1]" /> <span><?php _e('Relink to:', 'wpomatic') ?></span></label>
                <textarea name="campaign_word_relink[new1]"></textarea>
              </div>           
            </li>
            <?php endif ?>
          </ul>     
          
          <a href="#add_word" id="add_word"><?php _e('Add more', 'wpomatic') ?></a>
        </div>
                                           
        <!-- Options -->
        <div class="section" id="section_options">
          <?php if(isset($campaign_edit)): ?>
          <div class="section_warn">
            <img src="<?php echo $this->tplpath ?>/images/icon_alert.gif" alt="<?php _e('Warning', 'wpomatic') ?>" class="icon" />
            <h3><?php _e('Remember that', 'wpomatic') ?></h3>
            <p><?php _e('Changing these options only affects the creation of posts after the next time feeds are parsed.', 'wpomatic') ?></p>
            <p><?php _e('If you need to edit existing posts, you can do so by using the options under the Tools tab', 'wpomatic') ?></p>
          </div>
          <?php endif ?>
          
          <div class="checkbox">
            <label for="campaign_templatechk"><?php _e('Custom post template', 'wpomatic') ?></label> 
            <?php echo checkbox_tag('campaign_templatechk', 1, _data_value($data['main'], 'template')) ?>
            
            <div id="post_template" class="textarea <?php if(_data_value($data['main'], 'template', '{content}') !== '{content}') echo 'current' ?>">
              <?php echo textarea_tag('campaign_template', _data_value($data['main'], 'template', '{content}')) ?>
              <a href="#" id="enlarge_link"><?php _e('Enlarge', 'wpomatic') ?></a>
              
              <p class="note" id="tags_note">
                <?php _e('Valid tags:', 'wpomatic') ?>
              </p>
              <p id="tags_list">
                <span class="tag">{content}</span>, <span class="tag">{title}</span>, <span class="tag">{permalink}</span>, <span class="tag">{feedurl}</span>, <span class="tag">{feedtitle}</span>, <span class="tag">{feedlogo}</span>,<br /> <span class="tag">{campaigntitle}</span>, <span class="tag">{campaignid}</span>, <span class="tag">{campaignslug}</span>
              </p>
            </div>               
            
            <p class="note"><?php printf(__('Read about <a href="%s" class="help_link">post templates</a>, or check some <a href="%s" class="help_link">examples</a>', 'wpomatic'), $this->helpurl . 'post_templates', $this->helpurl . 'post_templates_examples') ?></p>            
          </div>
          
          <div class="multipletext">
            <?php 
              $f = _data_value($data['main'], 'frequency');

              if($f) {
                $frequency = WPOTools::calcTime($f);                
              }                
              else
                $frequency = array();
            ?>
            
            <label><?php _e('Frequency', 'wpomatic') ?></label>                                      

            <?php echo input_tag('campaign_frequency_d', _data_value($frequency, 'days', 1), 'size=2 maxlength=3')?>
            <?php _e('d', 'wpomatic') ?> 
            
            <?php echo input_tag('campaign_frequency_h', _data_value($frequency, 'hours', 5), 'size=2 maxlength=2')?>            
            <?php _e('h', 'wpomatic') ?> 
             
            <?php echo input_tag('campaign_frequency_m', _data_value($frequency, 'minutes', 0), 'size=2 maxlength=2')?>            
            <?php _e('m', 'wpomatic') ?> 
                
            <p class="note"><?php _e('How often should feeds be checked? (days, hours and minutes)', 'wpomatic') ?></p>
          </div>       
  
          <div class="checkbox">
            <?php echo label_for('campaign_cacheimages', __('Cache images', 'wpomatic')) ?>
            <?php echo checkbox_tag('campaign_cacheimages', 1, _data_value($data['main'], 'cacheimages', is_writable($this->cachepath))) ?>            
            <p class="note"><?php _e('Images will be stored in your server, instead of hotlinking from the original site.', 'wpomatic') ?> <a href="<?php echo $this->helpurl ?>image_caching" class="help_link"><?php _e('More', 'wpomatic') ?></a></p>
          </div>   
                                             
          <div class="checkbox">
            <?php echo label_for('campaign_feeddate', __('Use feed date', 'wpomatic')) ?>
            <?php echo checkbox_tag('campaign_feeddate', 1, _data_value($data['main'], 'feeddate', false)) ?>
            <p class="note"><?php _e('Use the original date from the post instead of the time the post is created by WP-o-Matic.', 'wpomatic') ?> <a href="<?php echo $this->helpurl ?>feed_date_option" class="help_link"><?php _e('More', 'wpomatic') ?></a></p>
          </div>    
          
          <div class="checkbox">
            <?php echo label_for('campaign_dopingbacks', __('Perform pingbacks', 'wpomatic')) ?>
            <?php echo checkbox_tag('campaign_dopingbacks', 1, _data_value($data['main'], 'dopingbacks', false)) ?>
          </div>         
          
          <div class="radio">
            <label class="main"><?php _e('Type of post to create', 'wpomatic')?></label>
              
            <?php echo radiobutton_tag('campaign_posttype', 'publish', !isset($data['main']['posttype']) || _data_value($data['main'], 'posttype') == 'publish', 'id=type_published') ?>
            <?php echo label_for('type_published', __('Published', 'wpomatic')) ?>
            
            <?php echo radiobutton_tag('campaign_posttype', 'private', _data_value($data['main'], 'posttype') == 'private', 'id=type_private') ?>
            <?php echo label_for('type_private', __('Private', 'wpomatic')) ?>
            
            <?php echo radiobutton_tag('campaign_posttype', 'draft', _data_value($data['main'], 'posttype') == 'draft', 'id=type_draft') ?>
            <?php echo label_for('type_draft', __('Draft', 'wpomatic')) ?>
          </div>
          
          <div class="text">
            <?php echo label_for('campaign_author', __('Author:', 'wpomatic')) ?>
            <?php echo select_tag('campaign_author', options_for_select($author_usernames, _data_value($data['main'], 'author', 'admin'))) ?>
            <p class="note"><?php _e("The created posts will be assigned to this author.", 'wpomatic') ?></p>
          </div>
          
          <div class="text required">
            <?php echo label_for('campaign_max', __('Max items to create on each fetch', 'wpomatic')) ?>
            <?php echo input_tag('campaign_max', _data_value($data['main'], 'max', '10'), 'size=2 maxlength=3') ?>
            <p class="note"><?php _e("Set it to 0 for unlimited. If set to a value, only the last X items will be selected, ignoring the older ones.", 'wpomatic') ?></p>
          </div>
          
          <div class="checkbox">            
            <?php echo label_for('campaign_linktosource', __('Post title links to source?', 'wpomatic')) ?>
            <?php echo checkbox_tag('campaign_linktosource', 1, _data_value($data['main'], 'linktosource', false)) ?>
          </div>
          
          <div class="radio">
            <label class="main"><?php _e('Discussion options:', 'wpomatic')?></label>
            
            <?php echo select_tag('campaign_commentstatus', 
                        options_for_select(
                          array('open' => __('Open', 'wpomatic'), 
                                'closed' => __('Closed', 'wpomatic'), 
                                'registered_only' => __('Registered only', 'registered_only')
                                ), _data_value($data['main'], 'comment_status', 'open'))) ?>
            
            <?php echo checkbox_tag('campaign_allowpings', 1, _data_value($data['main'], 'allowpings', true)) ?>
            <?php echo label_for('campaign_allowpings', __('Allow pings', 'wpomatic')) ?>
          </div>
        </div>   
              
        <?php if(isset($campaign_edit)): ?>              
        <!-- Tools -->
        <div class="section" id="section_tools">    
          <div class="buttons">
            <h3><?php _e('Posts action', 'wpomatic') ?></h3>
            <p class="note"><?php _e("The selected action applies to all the posts created by this campaign", 'wpomatic') ?></p>
            
            <ul>
              <li>
                <div class="btn">
                  <input type="submit" name="tool_removeall" value="<?php _e('Remove all', 'wpomatic') ?>" />
                </div>
              </li>
              <li>
                <div class="radio">
                  <label class="main"><?php _e('Change status to:', 'wpomatic')?></label>

                  <input type="radio" name="campaign_tool_changetype" value="publish" id="changetype_published" checked="checked" /> <label for="changetype_published"><?php _e('Published', 'wpomatic') ?></label>
                  <input type="radio" name="campaign_tool_changetype" value="private" id="changetype_private" /> <label for="changetype_private"><?php _e('Private', 'wpomatic') ?></label>
                  <input type="radio" name="campaign_tool_changetype" value="draft" id="changetype_draft" /> <label for="changetype_draft"><?php _e('Draft', 'wpomatic') ?></label>
                  <input type="submit" name="tool_changetype" value="<?php _e('Change', 'wpomatic') ?>" />
                </div>
              </li>
              <li>
                <div class="text">
                  <label for="campaign_tool_changeauthor"><?php _e('Change author username to:', 'wpomatic')?></label>
                  <?php echo select_tag('campaign_tool_changeauthor', options_for_select($author_usernames, _data_value($data['main'], 'author', 'admin'))) ?>
                  
                  <input type="submit" name="tool_changeauthor" value="<?php _e('Change', 'wpomatic') ?>" />
                </div>
              </li>
            </ul>
          </div>    
          
          <!-- 
          <div class="btn">   
            <label><?php _e('Test all feeds', 'wpomatic') ?></label>
            <input type="button" name="campaign_tool_testall_btn" value="Test" />
            <p class="note"><?php _e('This option creates one draft from each feed you added.', 'wpomatic') ?></p>
          </div>
          -->
        </div>
        <?php endif ?>
      </div>  
    </form>          
  </div>
  
<?php $this->adminFooter() ?>