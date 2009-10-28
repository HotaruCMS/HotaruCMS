<?php

require_once "PageLayout.php";

class DoubleBarLayout implements PageLayout
{

    public function fetchPagedLinks($parent, $queryVars, $hotaru) 
    {
        // NOTE: FRIENDLY URLS ARE NOT USED IN PAGINATION IN ADMIN (coz it makes things really difficult!)
        if ($hotaru->isAdmin == true) { $head = 'admin'; } else { $head = 'index'; }
        
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
        
        $currentPage = $parent->getPageNumber();
        $str = "";
        
        $before = 4;
        $after = 3;

        //write statement that handles the previous and next phases
           //if it is not the first page then write previous to the screen
        if (!$parent->isFirstPage()) {
            $previousPage = $currentPage - 1;
            if ($head == 'admin') { 
                $link = $path . '&pg=' . $previousPage;
            } else { 
                $parsed_query_args['pg'] = $previousPage;
                $link = $hotaru->url($parsed_query_args);
            }
            $str .= "<a class='pagi_previous' href='" . $link . "' title='" . $hotaru->lang['pagination_previous'] . "'>&laquo; " . $hotaru->lang['pagination_previous'] . "</a> \n";
        }
        
        // NOT FIRST PAGE
        if (!$parent->isFirstPage() && !($currentPage <= ($before + 1))) {
            if ($currentPage != 1) {
                if ($head == 'admin') { 
                    $link = $path . '&pg=1';
                } else { 
                    $parsed_query_args['pg'] = 1;
                    $link = $hotaru->url($parsed_query_args);
                }
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
                if ($head == 'admin') { 
                    $link = $path . '&pg=' . $i;
                } else { 
                    $parsed_query_args['pg'] = $i;
                    $link = $hotaru->url($parsed_query_args);
                }
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
            if ($head == 'admin') { 
                $link = $path . '&pg=' . $nextPage;
            } else { 
                $parsed_query_args['pg'] = $nextPage;
                $link = $hotaru->url($parsed_query_args);
            }
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