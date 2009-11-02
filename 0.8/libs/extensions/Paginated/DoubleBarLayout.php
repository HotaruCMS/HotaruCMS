<?php

require_once "PageLayout.php";

class DoubleBarLayout implements PageLayout
{

    public function fetchPagedLinks($parent, $queryVars, $hotaru) 
    {
        // NOTE: FRIENDLY URLS ARE NOT USED IN PAGINATION (I tried, but there's always *something* that screws up. Nick)
        if ($hotaru->isAdmin == true) { $head = 'admin_index.php?'; } else { $head = 'index.php?'; }
        
        // In Hotaru, $queryVars is always empty. We grab the whole path instead:
        $host = $hotaru->cage->server->getMixedString2('HTTP_HOST');
        $uri = $hotaru->cage->server->getMixedString2('REQUEST_URI');
        $path = "http://" . $host  . $uri;
        
        // But, for pagination, we can't just add pg=8 etc to the url because there's
        // quite likely a pg=X query variable already there! We need to strip that out:
        $query_args = parse_url($path, PHP_URL_QUERY);  // get all query vars
        if ($query_args) {
            $uri = str_replace($query_args, '', $uri);  // strip them from original $uri
            parse_str($query_args, $parsed_query_args); // split query vars into key->value pairs
            unset($parsed_query_args['pg']);   // we'll be replacing this in the links
            $path = "http://" . $host  . $uri . http_build_query($parsed_query_args); // rebuild url without page variable
        }
        
        // CONVERT FRIENDLY URLS INTO STANDARD URLS
        if ((FRIENDLY_URLS == 'true') && (!strrpos($path, '?'))) {
            $extras = str_replace(BASEURL, '', $path);
            $extras = rstrtrim($extras, '/');
            $bits = explode('/', $extras);
            $num_bits = count($bits);
            $path = BASEURL . $head;

            // if odd number of bits, the first must be a page, so...
            if ($num_bits % 2 == 1) {
                $path .= 'page=' . $bits[0];
                $finished_bit = array_shift($bits); // drop the first bit because we've just used it
                if ($bits) { $path .= '&'; }    // there are more bits so add &
            }
                        
            // now we're left with an even number of bits, so:
            $i = 0; // used for determining whether we need a ? or an =
            if ($bits) {
                foreach ($bits as $bit) {
                    if ($i % 2 == 0) { $path .= $bit . '='; } else { $path .= $bit . '&'; }
                    $i++;
                }
            }
            $path = rstrtrim($path, '&');
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
            $str .= "<a class='pagi_previous' href='" . $link . "' title='" . $hotaru->lang['pagination_previous'] . "'>&laquo; " . $hotaru->lang['pagination_previous'] . "</a> \n";
        }
        
        // NOT FIRST PAGE
        if (!$parent->isFirstPage() && !($currentPage <= ($before + 1))) {
            if ($currentPage != 1) {
                $link = $path . '&pg=1';
                $str .= "<a class='pagi_first' href='" . $link . "'  title='" . $hotaru->lang['pagination_first'] . "'>1</a> \n";
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
                if ($head == 'admin') { 
                    $link = $path . '&pg=' . $parent->fetchNumberPages();
                } else { 
                    $parsed_query_args['pg'] = $parent->fetchNumberPages();
                    $link = $hotaru->url($parsed_query_args);
                }
                $str .= "<a class='pagi_last' href='" . $link . "'  title='" . $hotaru->lang['pagination_last'] . "'>".$parent->fetchNumberPages()."</a> \n";
            }
        }
        
        // NOT LAST PAGE
        if (!$parent->isLastPage()) {
            $nextPage = $currentPage + 1;
            $link = $path . '&pg=' . $nextPage;
            $str .= "<a class='pagi_next' href='" . $link . "' title='" . $hotaru->lang['pagination_next'] . "'>" . $hotaru->lang['pagination_next'] . " &raquo;</a> \n";
        }
        
        // Wrap in a div
        $pagination = "<div id='pagination'>\n";
        $pagination .= $str;
        $pagination .= "</div>\n";
        
        return $pagination;
    }
}
?>