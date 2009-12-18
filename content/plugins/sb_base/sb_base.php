<?php
/**
 * name: SB Base
 * description: Social Bookmarking base - provides "list" and "post" templates. 
 * version: 0.1
 * folder: sb_base
 * class: SbBase
 * type: index
 * hooks: theme_index_top, header_meta, breadcrumbs, theme_index_main
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

class SbBase
{
    public $hotaru = '';   // access Hotaru functions using $this->hotaru
    
    /**
     * Determine the pageType
     */
    public function theme_index_top()
    {
        switch ($this->hotaru->pageName)
        {
            case 'index':
                $this->hotaru->pageType = 'list';
                $this->hotaru->pageTitle = $this->hotaru->lang["sb_base_site_name"];
                break;
            case 'post':
                $this->hotaru->pageType = 'post';
                break;
            default:
                $this->hotaru->pageType = 'list';
        }
    }
    
    
    /**
     * Match meta tag to a post's description (keywords is done in the Tags plugin)
     */
    public function header_meta()
    {    
        if ($this->hotaru->pageType != 'post') { return false; }
        $meta_content = sanitize($this->hotaru->post->content, 1);
        $meta_content = truncate($meta_content, 200);
        echo '<meta name="description" content="' . $meta_content . '">' . "\n";
        return true;
    }
    
    
    /**
     * Replace the default breadcrumbs in specific circumstances
     */
    public function breadcrumbs()
    {
        if ($this->hotaru->pageName == 'index') { 
            $this->hotaru->pageTitle = $this->hotaru->lang["sb_base_top"];
        }
    }
    
    /**
     * Determine which template to show and do preparation of variables, etc.
     */
    public function theme_index_main()
    {
        if (!$this->hotaru->pageName) { return false; }
        
        switch ($this->hotaru->pageType)
        {
            case 'post':
                $this->hotaru->displayTemplate('sb_list');
                break;
            default:
                $this->hotaru->vars['posts'] = array(); // = $this->hotaru->post->prepareList();
                $this->hotaru->displayTemplate('sb_list');
        }
    }


}
?>