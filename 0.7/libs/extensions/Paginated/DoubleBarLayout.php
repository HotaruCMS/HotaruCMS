<?php

require_once "PageLayout.php";

class DoubleBarLayout implements PageLayout
{

    public function fetchPagedLinks($parent, $queryVars) 
    {
        global $lang;
        
        $currentPage = $parent->getPageNumber();
        $str = "";
        
        $before = 4;
        $after = 3;

        //write statement that handles the previous and next phases
           //if it is not the first page then write previous to the screen
        if (!$parent->isFirstPage()) {
            $previousPage = $currentPage - 1;
            $str .= "<a class='previous' href='" . url(array('pg'=>$previousPage . $queryVars)) . "' title='" . $lang['pagination_previous'] . "'>&laquo; " . $lang['pagination_previous'] . "</a> ";
        }
        
        // NOT FIRST PAGE
        if (!$parent->isFirstPage() && !($currentPage <= ($before + 1))) {
            if ($currentPage != 1) {
                $str .= "<a class='first' href='" . url(array('pg'=>'1' . $queryVars)) . "'  title='" . $lang['pagination_first'] . "'>1</a> ";
                $str .= " <span class='dots'>...</span> ";
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
                $str .= "<span class='current'>$i</span>";
            }
            else {
                $str .= "<a class='page' href='" . url(array('pg'=>$i . $queryVars)) . "'>$i</a>";
            }
            if ($i != $currentPage + $after && $i != $parent->fetchNumberPages()) { $str .= ' '; }
            // ($i == $currentPage + $after || $i == $parent->fetchNumberPages()) ? $str .= " " : $str .= " | ";    // determine if to print bars or not
        } //end for

        //$str = rstrtrim($str, '| '); // trim trailing bar
              
        if (!$parent->isLastPage() && !($currentPage > ($parent->fetchNumberPages() - $after))) {
            if ($currentPage != $parent->fetchNumberPages() && $currentPage != $parent->fetchNumberPages() -1 && $currentPage != $parent->fetchNumberPages() - $before)
            {
                if ($currentPage < ($parent->fetchNumberPages() - ($after + 1))) { $str .= " <span class='dots'>...</span> "; }
                $str .= "<a class='last' href='" . url(array('pg'=>$parent->fetchNumberPages() . $queryVars)) . "'  title='" . $lang['pagination_last'] . "'>".$parent->fetchNumberPages()."</a> ";
            }
        }
        
        // NOT LAST PAGE
        if (!$parent->isLastPage()) {
            $nextPage = $currentPage + 1;
            $str .= "<a class='next' href='" . url(array('pg'=>$nextPage . $queryVars)) . "' title='" . $lang['pagination_next'] . "'>" . $lang['pagination_next'] . " &raquo;</a> ";
        }
        
        // Wrap in a div
        $pagination = "<div id='pagination'>";
        $pagination .= $str;
        $pagination .= "</div>";
        
        return $pagination;
    }
}
?>