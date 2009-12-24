<?php

require_once "PageLayout.php";

class DoubleBarLayout implements PageLayout
{

    public function fetchPagedLinks($parent, $h) 
    {
        // NOTE: FRIENDLY URLS ARE NOT USED IN PAGINATION (I tried, but there's always *something* that screws up. Nick)
        if ($h->isAdmin == true) { $head = 'admin_index.php?'; } else { $head = 'index.php?'; }
        
        // get full url from address bar
        $host = $h->cage->server->getMixedString2('HTTP_HOST');
        $uri = $h->cage->server->getMixedString2('REQUEST_URI');
        $path = "http://" . $host  . $uri;
        
        // if it doesn't contain $head, then it must be a friendly url 
        if ($path != BASEURL && !strrpos($path, $head)) {
            $path = $this->friendlyToStandardUrl($path, $head, $h);
        } 
        
        // add the head if we're on the top page (which doesn't have index.php attached) 
        if ($path == BASEURL) { $path = BASEURL . $head; }
        
        // But, for pagination, we can't just add pg=8 etc to the url because there's
        // quite likely a pg=X query variable already there! We need to strip that out:

        $query_args = parse_url($path, PHP_URL_QUERY);  // get all query vars
        
        if ($query_args) {
            $path = str_replace($query_args, '', $path);  // strip them from original $path
            parse_str($query_args, $parsed_query_args); // split query vars into key->value pairs
            unset($parsed_query_args['pg']);   // we'll be replacing pg in the links
            $path = $path . http_build_query($parsed_query_args); // rebuild url without pg parameter
        }
        
        $currentPage = $parent->getPageNumber();
        $str = "";
        
        $before = 4;
        $after = 3;

        //write statement that handles the previous and next phases
           //if it is not the first page then write previous to the screen
        if (!$parent->isFirstPage()) {
            $previousPage = $currentPage - 1;
            $link = $path . '&pg=' . $previousPage;
            $link = str_replace('?&', '?', $link); // we don't want an ampersand directly after a question mark
            $str .= "<a class='pagi_previous' href='" . $link . "' title='" . $h->lang['pagination_previous'] . "'>&laquo; " . $h->lang['pagination_previous'] . "</a> \n";
        }
        
        // NOT FIRST PAGE
        if (!$parent->isFirstPage() && !($currentPage <= ($before + 1))) {
            if ($currentPage != 1) {
                $link = $path . '&pg=1';
                $link = str_replace('?&', '?', $link); // we don't want an ampersand directly after a question mark
                $str .= "<a class='pagi_first' href='" . $link . "'  title='" . $h->lang['pagination_first'] . "'>1</a> \n";
                if ($currentPage > ($before+1)) {
                    $str .= " <span class='dots'>...</span> \n";
                }
            }
        }

        for ($i = $currentPage - $before; $i <= $currentPage + $after; $i++) {
            //if i is less than one then continue to next iteration        
            if ($i < 1) {
                continue;
            }
    
            if ($i > $parent->fetchNumberPages()) {
                break;
            }
    
            if ($i == $currentPage) {
                $str .= "<span class='pagi_current'>$i</span>\n";
            }
            else {
                $link = $path . '&pg=' . $i;
                $link = str_replace('?&', '?', $link); // we don't want an ampersand directly after a question mark
                $str .= "<a class='pagi_page' href='" . $link . "'>$i</a>\n";
            }
            if ($i != $currentPage + $after && $i != $parent->fetchNumberPages()) { $str .= ' '; }
            // ($i == $currentPage + $after || $i == $parent->fetchNumberPages()) ? $str .= " " : $str .= " | ";    // determine if to print bars or not
        } //end for

        //$str = rstrtrim($str, '| '); // trim trailing bar
              
        if (!$parent->isLastPage() && !($currentPage > ($parent->fetchNumberPages() - $after))) {
            if ($currentPage != $parent->fetchNumberPages() && $currentPage != $parent->fetchNumberPages() -1 && $currentPage != $parent->fetchNumberPages() - $before)
            {
                if ($currentPage < ($parent->fetchNumberPages() - ($after + 1))) { $str .= " <span class='pagi_dots'>...</span> \n"; }
                $link = $path . '&pg=' . $parent->fetchNumberPages();
                $link = str_replace('?&', '?', $link); // we don't want an ampersand directly after a question mark
                $str .= "<a class='pagi_last' href='" . $link . "'  title='" . $h->lang['pagination_last'] . "'>".$parent->fetchNumberPages()."</a> \n";
            }
        }
        
        // NOT LAST PAGE
        if (!$parent->isLastPage()) {
            $nextPage = $currentPage + 1;
            $link = $path . '&pg=' . $nextPage;
            $link = str_replace('?&', '?', $link); // we don't want an ampersand directly after a question mark
            $str .= "<a class='pagi_next' href='" . $link . "' title='" . $h->lang['pagination_next'] . "'>" . $h->lang['pagination_next'] . " &raquo;</a> \n";
        }
        
        // Wrap in a div
        $pagination = "<div id='pagination'>\n";
        $pagination .= $str;
        $pagination .= "</div>\n";
        
        return $pagination;
    }
    
    
    /**
     * Converts a friendly url into a standard one
     *
     * @param string $url
     * @param string $head - "index.php?" or "admin_index.php?"
     * @param object $h
     */
    public function friendlyToStandardUrl($url, $head, $h) 
    {
        // strip off BASEURL and trailing slash
        $url = str_replace(BASEURL, '', $url);
        $url = rtrim($url, '/');

        // start the standard url
        $standard_url = BASEURL . $head;
        
        // parts will hold the query vars
        $parts = array();
        $parts = explode('/', $url);
        
        // if odd number of query vars, the first is the page
        if (count($parts) % 2 == 1) {
             $page = array_shift($parts);
             $standard_url .= 'page=' . $page;
             if (!empty($parts)) { $standard_url .= '&'; }
        }
        
        // if query vars still in array, add them
        while (!empty($parts)) {
            $key = array_shift($parts);
            $value = array_shift($parts);
            $standard_url .= $key . '=' . $value;
            if (!empty($parts)) { $standard_url .= '&'; }
        }
        
        return $standard_url;
    }
}
?>
