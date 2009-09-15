<?php
/**
 * name: Search
 * description: Displays "Search!"
 * version: 0.1
 * folder: search
 * class: Search
 * requires: submit 0.4, sidebar_widgets 0.1
 * hooks: install_plugin, hotaru_header, header_include, post_list_filter
 *
 * Usage: Add <?php $plugins->pluginHook('search'); ?> to your theme, wherever you want to the "Search" form.
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

class Search extends PluginFunctions
{
    /**
     * Register search widget
     */
    public function install_plugin()
    {
        require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
        $sidebar = new Sidebar();
        $sidebar->addWidget('search', 'search', '');  // plugin name, function name, optional arguments
    } 
    
    /**
     * Include language
     */
    public function hotaru_header()
    {
        global $cage, $hotaru;
        
        $this->includeLanguage();
        
        // Get page title
        if ($cage->get->keyExists('search')) { $hotaru->setTitle($hotaru->pageToTitleCaps($cage->get->getMixedString2('search'))); }
    } 
    
    
    /**
     * Displays "Search!" wherever the plugin hook is.
     */
    public function sidebar_widget_search()
    {
        global $hotaru, $plugins, $lang;
        
        $hotaru->displayTemplate('search_box', 'search');
    }
    
    /**
     * Request the search results
     *
     * @return bool
     */
    public function theme_index_replace()
    {
        global $hotaru, $cage;
    
        if ($hotaru->isPage('search')) {
        
            $search_terms = $cage->get->getMixedString2('search');
            
            $this->search($search_terms);
    
            return true;
            
        } else {
            return false;
        }
        
        return false;
    }
    
    
    /**
     * Use the search terms to build a filter
     */
    public function post_list_filter()
    {
        global $hotaru, $cage, $filter, $lang, $page_title, $orderby, $select;
        
        if ($cage->get->keyExists('search')) 
        {
            $orig_search_terms = $cage->get->getMixedString2('search');
            $search_terms = $orig_search_terms;
            
            if ($search_terms)
            {
    
                // fetch sleect, orderby and filter,,,
                $prepared_search = $this->prepareSearchFilter($search_terms);
                extract($prepared_search);
                
                $rss = " <a href='" . url(array('page'=>'rss', 'search'=>stripslashes($orig_search_terms))) . "'>";
                $rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            
                $page_title = $lang["submit_page_breadcrumbs_search"] . " &raquo; " . stripslashes($orig_search_terms) . $rss;
                
                return true;    
            }
        }
        
        return false;    
    }
    
    /**
     * Prepare search filter
     */
    public function prepareSearchFilter($search)
    {
        $search_terms = strtolower($search);
        $search_terms = explode(" ", $search_terms);
        $search_terms = array_iunique($search_terms);
    
        $search_terms_clean = '';
        $full_index = true; // Do a full index (better) search if all terms are longer than 3 characters
        foreach($search_terms as $search_term) {
            if (strlen(trim($search_term)) < 4) {
                $full_index = false;
            }
            if ($this->isStopword($search_term) == false) {
                $search_terms_clean .= $search_term . " ";
            }
        }
        
        // Undo the filter that limits results to either 'top' or 'new' (See submit.php -> sub_prepare_list())
        if(isset($filter['post_status = %s'])) { unset($filter['post_status = %s']); }
        
        if($full_index) {
            $select = "*, MATCH(post_title, post_domain, post_url, post_content, post_tags) AGAINST ('" . $search_terms_clean . "') AS relevance";
            $orderby = "relevance DESC";        
            $filter["(post_status = 'top' OR post_status = 'new') AND MATCH (post_title, post_domain, post_url, post_content, post_tags) AGAINST (%s IN BOOLEAN MODE)"] = $search_terms_clean; 
        } else {
            $select = "*";
            $orderby = "post_date DESC";
            $where = explode_search('post_title', $search_terms_clean) . " OR ";
            $where .= explode_search('post_url', $search_terms_clean) . " OR ";
            $where .= explode_search('post_content', $search_terms_clean);
            $filter[$where] = "";
        }
        
        $prepared_search = array('select' => $select, 'orderby' => $orderby, 'filter' => $filter);
        
        return $prepared_search;
    }
    
    
    /** Explode search for short words
     * 
     * @param string $column
     * @param string $search_terms
     * @return string (with " OR " stripped off the end)
     */
    public function explodeSearch($column, $search_terms)
    {
        $query = '';
        
        foreach(explode(' ', trim($search_terms)) as $word){
            $query .= $column . " LIKE '%" . urlencode(" " . trim($word) . " ") . "%' OR ";
        }
        
        return substr($query, 0, -4);
    }
            
            
    /**
     * Is it a stopword?
     *
     *@return bool
     */
    public function isStopword($word)
    {
        $word_array = array();
    
         // list came from http://meta.wikimedia.org/wiki/MySQL_4.0.20_stop_word_list
        $stopwordlist = "things ii iii a able about above according accordingly across actually after afterwards again against ain't all allow allows almost alone along already also although always am among amongst an and another any anybody anyhow anyone anything anyway anyways anywhere apart appear appreciate appropriate are aren't around as aside ask asking associated at available away awfully be became because become becomes becoming been before beforehand behind being believe below beside besides best better between beyond both brief but by c'mon c's came can can't cannot cant cause causes certain certainly changes clearly co com come comes concerning consequently consider considering contain containing contains corresponding could couldn't course currently definitely described despite did didn't different do does doesn't doing don't done down downwards during each edu eg eight either else elsewhere enough entirely especially et etc even ever every everybody everyone everything everywhere ex exactly example except far few fifth first five followed following follows for former formerly forth four from further furthermore get gets getting given gives go goes going gone got gotten greetings had hadn't happens hardly has hasn't have haven't having he he's help hence her here here's hereafter hereby herein hereupon hers herself hi him himself his hither hopefully how howbeit however i'd i'll i'm i've ie if ignored immediate in inasmuch inc indeed indicate indicated indicates inner insofar instead into inward is isn't it it'd it'll it's its itself just keep keeps kept know knows known last lately later latter latterly least less lest let let's like liked likely little look looking looks ltd mainly many may maybe me mean meanwhile merely might more moreover most mostly much must my myself name namely nd near nearly necessary need needs neither never nevertheless new next nine no nobody non none noone nor normally not nothing novel now nowhere obviously of off often oh ok okay old on once one ones only onto or other others otherwise ought our ours ourselves out outside over overall own part particular particularly per perhaps placed please plus possible presumably probably provides que quite qv rather rd re really reasonably regarding regardless regards relatively respectively right said same saw say saying says second secondly see seeing seem seemed seeming seems seen self selves sensible sent serious seriously seven several shall she should shouldn't since six so some somebody somehow someone something sometime sometimes somewhat somewhere soon sorry specified specify specifying still sub such sup sure t's take taken tell tends th than thank thanks thanx that that's thats the their theirs them themselves then thence there there's thereafter thereby therefore therein theres thereupon these they they'd they'll they're they've think third this thorough thoroughly those though three through throughout thru thus to together too took toward towards tried tries truly try trying twice two un under unfortunately unless unlikely until unto up upon us use used useful uses using usually value various very via viz vs want wants was wasn't way we we'd we'll we're we've welcome well went were weren't what what's whatever when whence whenever where where's whereafter whereas whereby wherein whereupon wherever whether which while whither who who's whoever whole whom whose why will willing wish with within without won't wonder would would wouldn't yes yet you you'd you'll you're you've your yours yourself yourselves zero";
        
        $word_array = explode(' ', $stopwordlist);
    
        if (array_search($word, $word_array) == true) {
            return true;
        } else {
            return false;
        }
    }
    
}
?>