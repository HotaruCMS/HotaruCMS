<div class="wrap">
    <h2>Add New Campaign</h2>

    <form accept-charset="utf-8" method="post" action="" id="edit_campaign">
        <input type="hidden" value="21d2737262" name="_wpnonce" id="_wpnonce">
        <input type="hidden" value="/wp-admin/options-general.php?page=wpomatic.php&amp;s=add" name="_wp_http_referer">
        <input type="hidden" value="1" id="campaign_add" name="campaign_add">
        <ul class="submit" id="edit_buttons">
            <li><a class="help_link" href="http://bloglinkjapan.com/wp-content/plugins/wp-o-matic/help.php?item=campaigns">Help</a></li>
            <li><input type="submit" id="edit_submit" value="Submit" name="edit_submit"></li>
        </ul>

        <div id="admin_plugin_tabs">
            <ul class="tabs">
                <li><a id="tab_basic" href="#tab1">Basic</a></li>
                <li><a id="tab_feeds" href="#section_feeds">Feeds</a></li>
                <li><a id="tab_categories" href="#section_categories">Categories</a></li>
                <li><a id="tab_rewrite" href="#section_rewrite">Rewrite</a></li>
                <li><a id="tab_options" href="#section_options">Options</a></li>
            </ul>
        </div>

        <div id="tab_container">
            <!-- Basic section -->
            <div id="tab1" class="tab_content">
                <div class="longtext required">
                    <label for="campaign_title">Title</label>
                    <input type="text" value="" id="campaign_title" name="campaign_title">
                    <p class="note">Tip: pick a name that is general for all the campaign's feeds (eg: Paris Hilton)</p>
                </div>

                <div class="checkbox required">
                    <label for="campaign_active">Active?</label>
                    <input type="checkbox" checked="checked" value="1" id="campaign_active" name="campaign_active">
                    <p class="note">If inactive, the parser will ignore these feeds</p>
                </div>

                <div class="text">
                    <label for="campaign_slug">Campaign slug</label>
                    <input type="text" value="" id="campaign_slug" name="campaign_slug">
                    <p class="note">Optionally, you can set an identifier for this campaign. Useful for detailed track of your ad-revenue.</p>
                </div>
            </div>

            <!-- Feeds section -->
            <div id="section_feeds" class="tab_content">
                <p>Please fill in at least one feed. If you're not sure about the exact feed url, just type in the domain name, and the feed will be autodetected</p>

                <div id="edit_feed">

                    <div class="inlinetext required">
                        <label for="campaign_feed_new_0">Feed URL</label>
                        <input type="text" class="input_text" value="" id="campaign_feed_new_0" name="campaign_feed[new][]">
                    </div>
                    <div class="inlinetext required">
                        <label for="campaign_feed_new_1">Feed URL</label>
                        <input type="text" class="input_text" value="" id="campaign_feed_new_1" name="campaign_feed[new][]">
                    </div>
                    <div class="inlinetext required">
                        <label for="campaign_feed_new_2">Feed URL</label>
                        <input type="text" class="input_text" value="" id="campaign_feed_new_2" name="campaign_feed[new][]">
                    </div>
                    <div class="inlinetext required">
                        <label for="campaign_feed_new_3">Feed URL</label>
                        <input type="text" class="input_text" value="" id="campaign_feed_new_3" name="campaign_feed[new][]">
                    </div>
                </div>

                <a id="add_feed" href="#add_feed">Add more</a> | <a id="test_feeds" href="#">Check all</a>
            </div>

            <!-- Categories section -->
            <div id="section_categories" class="tab_content">
              <p>These are the categories where the posts will be created once they're fetched from the feeds.</p><p>You have to select at least one.</p>

              <ul id="categories">

                <li class="required pad0">
                <input type="checkbox" value="791" id="category_791" name="campaign_categories[]">
                <label for="category_791">Blogs</label></li>
                <li class="required pad1">
                <input type="checkbox" value="9912" id="category_9912" name="campaign_categories[]">
                <label for="category_9912">LifeYou.tv</label></li>
                <li class="required pad1">
                <input type="checkbox" value="12062" id="category_12062" name="campaign_categories[]">
                <label for="category_12062">YouTube</label></li>
              </ul>

              <a id="quick_add" href="#quick_add">Quick add</a>
            </div>

            <!-- Rewrite section -->
            <div id="section_rewrite" class="tab_content">
                <p>Want to transform a word into another? Or link a specific word to some website? <a class="help_link" href="http://bloglinkjapan.com/wp-content/plugins/wp-o-matic/help.php?item=campaign_rewrite">Read more</a></p>

                <ul id="edit_words">
                    <li class="word">
                        <div class="origin textarea">
                            <label for="campaign_word_origin_new1">Origin</label>
                            <textarea id="campaign_word_origin_new1" name="campaign_word_origin[new1]"></textarea>
                            <label class="regex"><input type="checkbox" name="campaign_word_option_regex[new1]"> <span>RegEx</span></label>
                        </div>
                        <div class="rewrite textarea">
                            <label><input type="checkbox" name="campaign_word_option_rewrite[new1]" value="1"> <span>Rewrite to:</span></label>
                            <textarea name="campaign_word_rewrite[new1]"></textarea>
                        </div>
                        <div class="relink textarea">
                            <label><input type="checkbox" name="campaign_word_option_relink[new1]" value="1"> <span>Relink to:</span></label>
                            <textarea name="campaign_word_relink[new1]"></textarea>
                        </div>
                    </li>
                </ul>

                <a id="add_word" href="#add_word">Add more</a>
            </div>

            <!-- Options -->
            <div id="section_options" class="tab_content">

              <div class="checkbox">
                <label for="campaign_templatechk">Custom post template</label>
                <input type="checkbox" value="1" id="campaign_templatechk" name="campaign_templatechk">
                <div class="textarea " id="post_template">
                  <textarea id="campaign_template" name="campaign_template">{content}</textarea>
                  <a id="enlarge_link" href="#">Enlarge</a>

                  <p id="tags_note" class="note">
                    Valid tags:              </p>
                  <p id="tags_list">
                    <span class="tag">{content}</span>, <span class="tag">{title}</span>, <span class="tag">{permalink}</span>, <span class="tag">{feedurl}</span>, <span class="tag">{feedtitle}</span>, <span class="tag">{feedlogo}</span>,<br> <span class="tag">{campaigntitle}</span>, <span class="tag">{campaignid}</span>, <span class="tag">{campaignslug}</span>
                  </p>
                </div>

                <p class="note">Read about <a class="help_link" href="http://bloglinkjapan.com/wp-content/plugins/wp-o-matic/help.php?item=post_templates">post templates</a>, or check some <a class="help_link" href="http://bloglinkjapan.com/wp-content/plugins/wp-o-matic/help.php?item=post_templates_examples">examples</a></p>
              </div>

              <div class="multipletext">

                <label>Frequency</label>

                <input type="text" maxlength="3" size="2" value="1" id="campaign_frequency_d" name="campaign_frequency_d">d
                <input type="text" maxlength="2" size="2" value="5" id="campaign_frequency_h" name="campaign_frequency_h">h
                <input type="text" maxlength="2" size="2" value="0" id="campaign_frequency_m" name="campaign_frequency_m">m

                <p class="note">How often should feeds be checked? (days, hours and minutes)</p>
              </div>

              <div class="checkbox">
                <label for="campaign_cacheimages">Cache images</label>
                <input type="checkbox" checked="checked" value="1" id="campaign_cacheimages" name="campaign_cacheimages">
                <p class="note">Images will be stored in your server, instead of hotlinking from the original site. <a class="help_link" href="http://bloglinkjapan.com/wp-content/plugins/wp-o-matic/help.php?item=image_caching">More</a></p>
              </div>

              <div class="checkbox">
                <label for="campaign_feeddate">Use feed date</label>
                <input type="checkbox" value="1" id="campaign_feeddate" name="campaign_feeddate">
                <p class="note">Use the original date from the post instead of the time the post is created by WP-o-Matic. <a class="help_link" href="http://bloglinkjapan.com/wp-content/plugins/wp-o-matic/help.php?item=feed_date_option">More</a></p>
              </div>

              <div class="checkbox">
                <label for="campaign_dopingbacks">Perform pingbacks</label>
                <input type="checkbox" value="1" id="campaign_dopingbacks" name="campaign_dopingbacks">
              </div>

              <div class="radio">
                <label class="main">Type of post to create</label>

                <input type="radio" checked="checked" value="publish" id="type_published" name="campaign_posttype">
                <label for="type_published">Published</label>
                <input type="radio" value="private" id="type_private" name="campaign_posttype">
                <label for="type_private">Private</label>
                <input type="radio" value="draft" id="type_draft" name="campaign_posttype">
                <label for="type_draft">Draft</label>
              </div>

              <div class="text">
                <label for="campaign_author">Author:</label>
                <select id="campaign_author" name="campaign_author"><option value="acm2001">admin (acm2001)</option>
                <option value="raymond">raymond</option>
                </select>
                <p class="note">The created posts will be assigned to this author.</p>
              </div>

              <div class="text required">
                <label for="campaign_max">Max items to create on each fetch</label>
                <input type="text" maxlength="3" size="2" value="10" id="campaign_max" name="campaign_max">
                <p class="note">Set it to 0 for unlimited. If set to a value, only the last X items will be selected, ignoring the older ones.</p>
              </div>

              <div class="checkbox">
                <label for="campaign_linktosource">Post title links to source?</label>
                <input type="checkbox" value="1" id="campaign_linktosource" name="campaign_linktosource">
              </div>

              <div class="radio">
                <label class="main">Discussion options:</label>

                <select id="campaign_commentstatus" name="campaign_commentstatus">
                    <option selected="selected" value="open">Open</option>
                    <option value="closed">Closed</option>
                    <option value="registered_only">Registered only</option>
                </select>
                <input type="checkbox" checked="checked" value="1" id="campaign_allowpings" name="campaign_allowpings">
                <label for="campaign_allowpings">Allow pings</label>
              </div>
            </div>

        </div>
    </form>
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
            $(".tab_content").hide(); //Hide all tab content

            var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
            $(activeTab).fadeIn(); //Fade in the active ID content
            return false;
        });
     });
</script>