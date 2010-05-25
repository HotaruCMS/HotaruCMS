<?php
/**
 * name: Bookmarking
 * description: Social Bookmarking base - provides "list" and "post" templates. 
 * version: 0.1
 * folder: bookmarking
 * class: Bookmarking
 * type: base
 * hooks: install_plugin, theme_index_top, header_meta, header_include, navigation, breadcrumbs, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings, admin_theme_main_stats, user_settings_pre_save, user_settings_fill_form, user_settings_extra_settings, theme_index_pre_main, profile_navigation, post_rss_feed_items
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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

class Bookmarking
{
	/**
	 * Install Submit settings if they don't already exist
	 */
	public function install_plugin($h)
	{
		// Default settings 
		$bookmarking_settings = $h->getSerializedSettings();
		if (!isset($bookmarking_settings['posts_per_page'])) { $bookmarking_settings['posts_per_page'] = 10; }
		if (!isset($bookmarking_settings['rss_redirect'])) { $bookmarking_settings['rss_redirect'] = ''; }
		if (!isset($bookmarking_settings['archive'])) { $bookmarking_settings['archive'] = "no_archive"; }
		$h->updateSetting('bookmarking_settings', serialize($bookmarking_settings));
		
		// Add "open in new tab" option to the default user settings
		$base_settings = $h->getDefaultSettings('base'); // originals from plugins
		$site_settings = $h->getDefaultSettings('site'); // site defaults updated by admin
		if (!isset($base_settings['new_tab'])) { 
			$base_settings['new_tab'] = ""; $site_settings['new_tab'] = "";
			$h->updateDefaultSettings($base_settings, 'base'); 
			$h->updateDefaultSettings($site_settings, 'site');
		}
		if (!isset($base_settings['link_action'])) { 
			$base_settings['link_action'] = ""; $site_settings['link_action'] = "";
			$h->updateDefaultSettings($base_settings, 'base'); 
			$h->updateDefaultSettings($site_settings, 'site');
		}
	}


	/**
	 * theme_index_top
	 */
	public function theme_index_top($h)
	{
		$this->determinePage($h);
		// run all other theme_index_top functions except this one
		$h->pluginHook('theme_index_top', '', array(), array('bookmarking'));
		$this->finalizePage($h);
		return "skip";
	}
	

	/**
	 * Determine the page
	 */
	public function determinePage($h)
	{
		// check if we're using the sort/filter links
		if ($h->cage->get->keyExists('sort')) {
			$h->pageName = 'sort';
		}

		// check if we should forward an RSS link to its source
		$this->rssForwarding($h);
		
		//check if we should set the home page to "popular"
		$this->setHomePopular($h);

		// check page name and set types and titles
		$this->checkPageName($h);
	}
	
	
	/**
	 * We should now know the pageName for certain, so finish setting up the page
	 */
	public function finalizePage($h)
	{
		// no need to continue for other types of homepage
		$valid_lists = array('popular', 'upcoming', 'latest', 'all');
		if (($h->pageName == $h->home) && (!in_array($h->home, $valid_lists))) { return false; }

		// stop here if not a list or the pageType has been set elsewhere:
		if (!empty($h->pageType) && ($h->pageType != 'list') && ($h->pageType != 'post')) {
			return false; 
		}
		
		// get the BookmarkingFunctions class
		$funcs = $this->getBookmarkingFunctions($h);

		// get settings
		$h->vars['bookmarking_settings'] = $h->getSerializedSettings('bookmarking');
		$posts_per_page = $h->vars['bookmarking_settings']['posts_per_page'];
		
		// if a list, get the posts:
		switch ($h->pageType)
		{
			case 'list':
				$post_count = $funcs->prepareList($h, '', 'count');   // get the number of posts
				$post_query = $funcs->prepareList($h, '', 'query');   // and the SQL query used
				$h->vars['pagedResults'] = $h->pagination($post_query, $post_count, $posts_per_page, 'posts');
				break;
			case 'post':
				// if a post is already set (e.g. from the scategories plugin), we don't want to
				// do the default stuff below. We do, however, need the "target", "editorial" stuff after it, though...
				break;
			default:
				// Probably a post, let's check:
				if (is_numeric($h->pageName)) {
					// Page name is a number so it must be a post with non-friendly urls
					$exists = $h->readPost($h->pageName);    // read current post
					if (!$exists) { $h->pageTitle = $h->lang['main_theme_page_not_found']; return false; }
					$h->pageTitle = $h->post->title;
					$h->pageType = 'post';
				} elseif ($post_id = $h->isPostUrl($h->pageName)) {
					// Page name belongs to a story
					$h->readPost($post_id);    // read current post
					$h->pageTitle = $h->post->title;
					$h->pageType = 'post';
				}
		} // close switch
		
		// user defined settings:
		
		if (!$h->currentUser->settings) { 
			// logged out users get the default settings:
			$h->currentUser->settings = $h->getDefaultSettings('site');
		}
		
		// open links in a new tab?
		if ($h->currentUser->settings['new_tab']) { 
			$h->vars['target'] = 'target="_blank"'; 
		} else { 
			$h->vars['target'] = ''; 
		}
		
		// open link to the source or the site post?
		if ($h->currentUser->settings['link_action']) { 
			$h->vars['link_action'] = 'source'; 
		} else { 
			$h->vars['link_action'] = ''; 
		}
		
		// editorial (story with an internal link)
		if (strstr($h->post->origUrl, BASEURL)) { 
			$h->vars['editorial'] = true;
		} else { 
			$h->vars['editorial'] = false; 
		} 
		
		// get settings from Submit 
		if (!isset($h->vars['submit_settings'])) {
			$h->vars['submit_settings'] = $h->getSerializedSettings('submit');
		}
	}
	

	/**
	 * Check if we should forward an RSS link to its source
	 */
	public function rssForwarding($h)
	{
		$h->pluginHook('posts_pre_rss_forward');
		 
		// check if this is an RSS link forwarding to the source
		if (!$h->cage->get->keyExists('forward')) { return false; }
		
		$post_id = $h->cage->get->testInt('forward');
		if ($post_id) { $post = $h->getPost($post_id); }
		if (isset($post->post_orig_url)) {
			header("Location:" . urldecode($post->post_orig_url));
			exit;
		}
	}
	
	
	/**
	 * Check if we should set the home page to Popular
	 */
	public function setHomePopular($h)
	{
		$h->pluginHook('posts_pre_set_home');

		// Allow Bookmarking to set the homepage to "popular" unless already set.
		if (!$h->home) {
			$h->setHome('popular', 'popular'); // and set name to "popular", too, if not already set.
		}
	}

	/**
	 * Get Bookmarking Functions
	 */
	public function getBookmarkingFunctions($h)
	{
		// include bookmarking_functions class:
		require_once(PLUGINS . 'bookmarking/libs/BookmarkingFunctions.php');
		return new BookmarkingFunctions();
	}
	

	/**
	 * Check page name and set types and titles
	 */
	public function checkPageName($h)
	{
		switch ($h->pageName)
		{
			case 'popular':
				$h->pageType = 'list';
				$h->pageTitle =  ($h->home == 'popular') ? $h->lang["bookmarking_site_name"] : $h->lang["bookmarking_top"];
				break;
			case 'latest':
				$h->pageType = 'list';
				$h->pageTitle = $h->lang["bookmarking_latest"];
				break;
			case 'upcoming':
				$h->pageType = 'list';
				$h->pageTitle = $h->lang["bookmarking_upcoming"];
				break;
			case 'all':
				$h->pageType = 'list';
				$h->pageTitle = $h->lang["bookmarking_all"];
				break;
			case 'sort':
				$sort = $h->cage->get->testPage('sort');
				if ($sort) {
					$h->pageType = 'list';
					$sort_lang = 'bookmarking_' . str_replace('-', '_', $sort);
					$h->pageTitle = $h->lang[$sort_lang];
				}
				break;
			default:
				// no default or we'd mess up anything set by other plugins
		}
		
		// case for paginated pages, but *no pagename*
		if ((!$h->pageName || $h->pageName == 'popular') && $h->cage->get->keyExists('pg')) {
			if (!$h->home) { $h->setHome('popular'); } // query vars previously prevented getPageName returning a name
			$h->pageName = 'popular';
			$h->pageType = 'list';
			$h->pageTitle = $h->lang["bookmarking_top"]; 
		}
	}
	
	
	/**
	 * Match meta tag to a post's description (keywords is done in the Tags plugin)
	 */
	public function header_meta($h)
	{
		if ($h->pageType != 'post') { return false; }
		$meta_content = sanitize($h->post->content, 'all');
		$meta_content = truncate($meta_content, 200);
		echo '<meta name="description" content="' . $meta_content . '" />' . "\n";
		return true;
	}
	
	
	/**
	 * Add "Latest" to the navigation bar
	 */
	public function navigation($h)
	{
		if ($h->home != 'popular') {
			// highlight "Top Stories" as active tab
			if ($h->pageName == 'popular') { $status = "id='navigation_active'"; } else { $status = ""; }
			
			// display the link in the navigation bar
			echo "<li><a  " . $status . " href='" . $h->url(array('page'=>'popular')) . "'>" . $h->lang["bookmarking_top"] . "</a></li>\n";
		}
		
		// highlight "Latest" as active tab
		if ($h->pageName == 'latest') { $status = "id='navigation_active'"; } else { $status = ""; }
		
		// display the link in the navigation bar
		echo "<li><a  " . $status . " href='" . $h->url(array('page'=>'latest')) . "'>" . $h->lang["bookmarking_latest"] . "</a></li>\n";
	}


	/**
	 * Replace the default breadcrumbs in specific circumstances
	 */
	public function breadcrumbs($h)
	{
		if ($h->subPage) { return false; } // don't use these breadcrumbs if on a subpage 
		
		if ($h->pageName == 'popular') { 
			$h->pageTitle = $h->lang["bookmarking_top"];
		}
		
		switch ($h->pageName) {
			case 'popular':
				return $h->pageTitle . ' ' . $h->rssBreadcrumbsLink('top');
				break;
			case 'latest':
				return $h->pageTitle . ' ' . $h->rssBreadcrumbsLink('new');
				break;
			case 'upcoming':
				return $h->pageTitle . ' ' . $h->rssBreadcrumbsLink('upcoming');
				break;
			case 'all':
				return $h->pageTitle . ' ' . $h->rssBreadcrumbsLink();
				break;
		}
	}


    /**
     * Determine which template to show and do preparation of variables, etc.
     */
    public function theme_index_main($h)
    {
        // stop here if not a list of a post
        if (($h->pageType != 'list') && ($h->pageType != 'post')) { return false; }
        
        // necessary settings:
        $h->vars['use_content'] = $h->vars['submit_settings']['content'];
        $h->vars['use_summary'] = $h->vars['submit_settings']['summary'];
        $h->vars['summary_length'] = $h->vars['submit_settings']['summary_length'];
        
        switch ($h->pageType)
        {
            case 'post':
                // This post is visible if it's not buried/pending OR if the viewer has edit post permissions...
                
                // defaults:
                $buried = false; $pending = false; $can_edit = false;
                
                // check if buried:
                if ($h->post->status == 'buried') {
                    $buried = true;
                    $h->messages[$h->lang["bookmarking_post_buried"]] = "red";
                } 
                
                // check if pending:
                if ($h->post->status == 'pending') { 
                    $pending = true;
                    $h->messages[$h->lang["bookmarking_post_pending"]] = "red";
                }
                
                // check if global edit permissions
                if ($h->currentUser->getPermission('can_edit_posts') == 'yes') { $can_edit = true; }

                $h->showMessages();
                
                // display post or show error message
                if (!$buried && !$pending){
                    $h->displayTemplate('bookmarking_post');
                } elseif ($can_edit) {
                    $h->displayTemplate('bookmarking_post');
                } else {
                    // don't show the post
                }
                
                return true;
                break;
                
            case 'list':
                if (isset($h->vars['pagedResults']->items)) {
                    $h->displayTemplate('bookmarking_list');
                    echo $h->pageBar($h->vars['pagedResults']);
                } else {
                    $h->displayTemplate('bookmarking_no_posts');
                }
                return true;
        }
    }

    
    /**
     * Show stats on Admin home page
     */
    public function admin_theme_main_stats($h, $vars)
    {
		echo "<li>&nbsp;</li>";
		foreach ($vars as $key => $value) {
			echo "<li class='title'>" . $key . "</li>";
			foreach ($value as $stat_type) {
				$posts = $h->post->stats($h, $stat_type);
				if (!$posts) { $posts = 0; }
				$lang_name = 'bookmarking_admin_stats_' . $stat_type;
				echo "<li>" . $h->lang[$lang_name] . ": " . $posts . "</li>";
			}
		}
	}
    
    
    /**
     * User Settings - before saving
     */
    public function user_settings_pre_save($h)
    {
        // Open posts in a new tab?
        if ($h->cage->post->getAlpha('new_tab') == 'yes') { 
            $h->vars['settings']['new_tab'] = "checked"; 
        } else { 
            $h->vars['settings']['new_tab'] = "";
        }
        
        // List links open source url or post page?
        if ($h->cage->post->getAlpha('link_action') == 'source') { 
            $h->vars['settings']['link_action'] = "checked"; 
        } else { 
            $h->vars['settings']['link_action'] = "";
        }
    }
    
    
    /**
     * User Settings - fill the form
     */
    public function user_settings_fill_form($h)
    {
        if (!isset($h->vars['settings']) || !$h->vars['settings']) { return false; }
        
        if ($h->vars['settings']['new_tab']) { 
            $h->vars['new_tab_yes'] = "checked"; 
            $h->vars['new_tab_no'] = ""; 
        } else { 
            $h->vars['new_tab_yes'] = ""; 
            $h->vars['new_tab_no'] = "checked"; 
        }
        
        if ($h->vars['settings']['link_action']) { 
            $h->vars['link_action_source'] = "checked"; 
            $h->vars['link_action_post'] = ""; 
        } else { 
            $h->vars['link_action_source'] = ""; 
            $h->vars['link_action_post'] = "checked"; 
        }
    }
    
    
    /**
     * User Settings - html for form
     */
    public function user_settings_extra_settings($h)
    {
        if (!isset($h->vars['settings']) || !$h->vars['settings']) { return false; }
        
        echo "<tr>\n";
            // OPEN POSTS IN A NEW TAB?
        echo "<td>" . $h->lang['bookmarking_users_settings_open_new_tab'] . "</td>\n";
        echo "<td><input type='radio' name='new_tab' value='yes' " . $h->vars['new_tab_yes'] . "> " . $h->lang['users_settings_yes'] . " &nbsp;&nbsp;\n";
        echo "<input type='radio' name='new_tab' value='no' " . $h->vars['new_tab_no'] . "> " . $h->lang['users_settings_no'] . "</td>\n";
        echo "</tr>\n";
        
        echo "<tr>\n";
            // OPEN POSTS IN A NEW TAB?
        echo "<td>" . $h->lang['bookmarking_users_settings_link_action'] . "</td>\n";
        echo "<td><input type='radio' name='link_action' value='source' " . $h->vars['link_action_source'] . "> " . $h->lang['bookmarking_users_settings_source'] . " &nbsp;&nbsp;\n";
        echo "<input type='radio' name='link_action' value='post' " . $h->vars['link_action_post'] . "> " . $h->lang['bookmarking_users_settings_post'] . "</td>\n";
        echo "</tr>\n";
    }
    
    
    /** 
     * Add sorting options
     */
    public function submit_post_breadcrumbs($h)
    {
        if ($h->isPage('submit2')) { return false; } // don't show sorting on Submit Confirm
        
        // exit if this isn't a page of type list, user or profile
        $page_type = $h->pageType;
        if ($page_type != 'list' && $page_type != 'user' && $page_type != 'profile') { return false; }
        
        // go set up the links
        $this->setUpSortLinks($h);
        

    }
    
    
    /**
     * Profile navigation link
     */
    public function profile_navigation($h)
    {
        echo "<li><a href='" . $h->url(array('page'=>'all', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang["users_all_posts"] . "</a></li>\n";
    }
    
    
    /** 
     * Prepare sort links
     */
    public function theme_index_pre_main($h)
    {
        $pagename = $h->pageName;
        
        // check if we're looking at a category
        if ($h->subPage == 'category') { 
            $category = $h->vars['category_id'];
        } 
        
        // check if we're looking at a tag
        if ($h->subPage == 'tags') { 
            $tag = $h->vars['tag'];
        } 
        
        // check if we're looking at a media type
        if ($h->cage->get->keyExists('media')) { 
            $media = $h->cage->get->testAlnumLines('media');
        } 
        
        // check if we're looking at a user
        if ($h->cage->get->keyExists('user')) { 
            $user = $h->cage->get->testUsername('user');
        } 
        
        // check if we're looking at a sorted page
        if ($h->cage->get->keyExists('sort')) { 
            $sort = $h->cage->get->testAlnumLines('sort');
        } 
        
        // POPULAR LINK
        if (isset($category)) { $url = $h->url(array('page'=>'popular', 'category'=>$category));
         } elseif (isset($tag)) { $url = $h->url(array('page'=>'popular', 'tag'=>$tag));
         } elseif (isset($media)) { $url = $h->url(array('page'=>'popular', 'media'=>$media));
         } elseif (isset($user)) { $url = $h->url(array('page'=>'popular', 'user'=>$user));
         } else { $url = $h->url(array('page'=>'popular',)); } 
        $h->vars['popular_link'] = $url;
         
        // POPULAR ACTIVE OR INACTIVE
        if (($pagename == 'popular') && (!isset($sort)) && $h->pageType != 'profile') { 
            $h->vars['popular_active'] = "class='active'";
        } else { $h->vars['popular_active'] = ""; }
        
        // UPCOMING LINK
        if (isset($category)) { $url = $h->url(array('page'=>'upcoming', 'category'=>$category));
         } elseif (isset($tag)) { $url = $h->url(array('page'=>'upcoming', 'tag'=>$tag));
         } elseif (isset($media)) { $url = $h->url(array('page'=>'upcoming', 'media'=>$media));
         } elseif (isset($user)) { $url = $h->url(array('page'=>'upcoming', 'user'=>$user));
         } else { $url = $h->url(array('page'=>'upcoming')); }
        $h->vars['upcoming_link'] = $url;
        
        // UPCOMING ACTIVE OR INACTIVE
        if ($pagename == 'upcoming' && !isset($sort)) { 
            $h->vars['upcoming_active'] = "class='active'";
        } else { $h->vars['upcoming_active'] = ""; }
        
        // LATEST LINK
        if (isset($category)) { $url = $h->url(array('page'=>'latest', 'category'=>$category));
         } elseif (isset($tag)) { $url = $h->url(array('page'=>'latest', 'tag'=>$tag));
         } elseif (isset($media)) { $url = $h->url(array('page'=>'latest', 'media'=>$media));
         } elseif (isset($user)) { $url = $h->url(array('page'=>'latest', 'user'=>$user));
         } else { $url = $h->url(array('page'=>'latest')); }
        $h->vars['latest_link'] = $url;

        // LATEST ACTIVE OR INACTIVE
        if ($pagename == 'latest' && !isset($sort)) { 
            $h->vars['latest_active'] = "class='active'";
        } else { $h->vars['latest_active'] = ""; }
        
        // ALL LINK
        if (isset($category)) { $url = $h->url(array('page'=>'all', 'category'=>$category));
         } elseif (isset($tag)) { $url = $h->url(array('page'=>'all', 'tag'=>$tag));
         } elseif (isset($media)) { $url = $h->url(array('page'=>'all', 'media'=>$media));
         } elseif (isset($user)) { $url = $h->url(array('page'=>'all', 'user'=>$user));
         } else { $url = $h->url(array('page'=>'all')); }
        $h->vars['all_link'] = $url;

        // ALL ACTIVE OR INACTIVE
        if ($pagename == 'all' && !isset($sort)) { 
            $h->vars['all_active'] = "class='active'";
        } else { $h->vars['all_active'] = ""; }
        
        // 24 HOURS LINK
        if (isset($category)) { $url = $h->url(array('sort'=>'top-24-hours', 'category'=>$category));
         } elseif (isset($tag)) { $url = $h->url(array('sort'=>'top-24-hours', 'tag'=>$tag));
         } elseif (isset($media)) { $url = $h->url(array('sort'=>'top-24-hours', 'media'=>$media));
         } elseif (isset($user)) { $url = $h->url(array('sort'=>'top-24-hours', 'user'=>$user));
         } else { $url = $h->url(array('sort'=>'top-24-hours')); }
        $h->vars['24_hours_link'] = $url;

        // 24 HOURS ACTIVE OR INACTIVE
        if (isset($sort) && $sort == 'top-24-hours') { 
            $h->vars['top_24_hours_active'] = "class='active'";
        } else { $h->vars['top_24_hours_active'] = ""; }
        
        // 48 HOURS LINK
        if (isset($category)) { $url = $h->url(array('sort'=>'top-48-hours', 'category'=>$category));
         } elseif (isset($tag)) { $url = $h->url(array('sort'=>'top-48-hours', 'tag'=>$tag));
         } elseif (isset($media)) { $url = $h->url(array('sort'=>'top-48-hours', 'media'=>$media));
         } elseif (isset($user)) { $url = $h->url(array('sort'=>'top-48-hours', 'user'=>$user));
         } else { $url = $h->url(array('sort'=>'top-48-hours')); }
        $h->vars['48_hours_link'] = $url;

        // 48 HOURS ACTIVE OR INACTIVE
        if (isset($sort) && $sort == 'top-48-hours') { 
            $h->vars['top_48_hours_active'] = "class='active'";
        } else { $h->vars['top_48_hours_active'] = ""; }
        
        // 7 DAYS LINK
        if (isset($category)) { $url = $h->url(array('sort'=>'top-7-days', 'category'=>$category));
         } elseif (isset($tag)) { $url = $h->url(array('sort'=>'top-7-days', 'tag'=>$tag));
         } elseif (isset($media)) { $url = $h->url(array('sort'=>'top-7-days', 'media'=>$media));
         } elseif (isset($user)) { $url = $h->url(array('sort'=>'top-7-days', 'user'=>$user));
         } else { $url = $h->url(array('sort'=>'top-7-days')); }
        $h->vars['7_days_link'] = $url;

        // 7 DAYS ACTIVE OR INACTIVE
        if (isset($sort) && $sort == 'top-7-days') { 
            $h->vars['top_7_days_active'] = "class='active'";
        } else { $h->vars['top_7_days_active'] = ""; }
        
        // 30 DAYS LINK
        if (isset($category)) { $url = $h->url(array('sort'=>'top-30-days', 'category'=>$category));
         } elseif (isset($tag)) { $url = $h->url(array('sort'=>'top-30-days', 'tag'=>$tag));
         } elseif (isset($media)) { $url = $h->url(array('sort'=>'top-30-days', 'media'=>$media));
         } elseif (isset($user)) { $url = $h->url(array('sort'=>'top-30-days', 'user'=>$user));
         } else { $url = $h->url(array('sort'=>'top-30-days')); }
        $h->vars['30_days_link'] = $url;

        // 30 DAYS ACTIVE OR INACTIVE
        if (isset($sort) && $sort == 'top-30-days') { 
            $h->vars['top_30_days_active'] = "class='active'";
        } else { $h->vars['top_30_days_active'] = ""; }
        
        // 365 DAYS LINK
        if (isset($category)) { $url = $h->url(array('sort'=>'top-365-days', 'category'=>$category));
         } elseif (isset($tag)) { $url = $h->url(array('sort'=>'top-365-days', 'tag'=>$tag));
         } elseif (isset($media)) { $url = $h->url(array('sort'=>'top-365-days', 'media'=>$media));
         } elseif (isset($user)) { $url = $h->url(array('sort'=>'top-365-days', 'user'=>$user));
         } else { $url = $h->url(array('sort'=>'top-365-days')); }
        $h->vars['365_days_link'] = $url;

        // 365 DAYS ACTIVE OR INACTIVE
        if (isset($sort) && $sort == 'top-365-days') { 
            $h->vars['top_365_days_active'] = "class='active'";
        } else { $h->vars['top_365_days_active'] = ""; }
        
        // ALL TIME LINK
        if (isset($category)) { $url = $h->url(array('sort'=>'top-all-time', 'category'=>$category));
         } elseif (isset($tag)) { $url = $h->url(array('sort'=>'top-all-time', 'tag'=>$tag));
         } elseif (isset($media)) { $url = $h->url(array('sort'=>'top-all-time', 'media'=>$media));
         } elseif (isset($user)) { $url = $h->url(array('sort'=>'top-all-time', 'user'=>$user));
         } else { $url = $h->url(array('sort'=>'top-all-time')); }
        $h->vars['all_time_link'] = $url;
        
        // ALL TIME ACTIVE OR INACTIVE
        if (isset($sort) && $sort == 'top-all-time') { 
            $h->vars['top_all_time_active'] = "class='active'";
        } else { $h->vars['top_all_time_active'] = ""; }
        
        // display the sort links
        $h->displayTemplate('bookmarking_sort_filter');
    }
    
    
    public function post_rss_feed_items($h, $args = array())
    {
        $bookmarking_settings = $h->getSerializedSettings('bookmarking');
        
        $result = $args['result'];
        $item = $h->vars['post_rss_item'];
        
        // if RSS redirecting is enabled, append forward=1 to the url
        if (isset($bookmarking_settings['rss_redirect']) && !empty($bookmarking_settings['rss_redirect'])) {
            $item['link'] = $h->url(array('page'=>$result->post_id, 'forward'=>$result->post_id));
        } else {
            $item['link'] = $h->url(array('page'=>$result->post_id));
        }
        
        $h->vars['post_rss_item'] = $item;
    }
}
?>
