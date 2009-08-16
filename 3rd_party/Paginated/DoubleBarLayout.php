<?php

require_once "PageLayout.php";

class DoubleBarLayout implements PageLayout
{

    public function fetchPagedLinks($parent, $queryVars) 
    {
    
        $currentPage = $parent->getPageNumber();
        $str = "";
        
        $before = 3;
        $after = 3;

        // NOT FIRST PAGE
        if (!$parent->isFirstPage()) {
            if ($currentPage != 1) {
                $str .= "<a class='first' href='" . url(array('pg'=>'1' . $queryVars)) . "'  title='First'>&lt;&lt;</a> ";
            }
        }

        //write statement that handles the previous and next phases
           //if it is not the first page then write previous to the screen
        if (!$parent->isFirstPage()) {
            $previousPage = $currentPage - 1;
            $str .= "<a class='previous' href='" . url(array('pg'=>$previousPage . $queryVars)) . "' title='Previous'>&lt;</a> ";
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
              
        if (!$parent->isLastPage()) {
            if ($currentPage != $parent->fetchNumberPages() && $currentPage != $parent->fetchNumberPages() -1 && $currentPage != $parent->fetchNumberPages() - $before)
            {
                $str .= " <span class='dots'>...</span> ";
                $str .= "<a class='last' href='" . url(array('pg'=>$parent->fetchNumberPages() . $queryVars)) . "'  title='Last'>".$parent->fetchNumberPages()."</a> ";
            }
        }
        
        // NOT LAST PAGE
        if (!$parent->isLastPage()) {
            $nextPage = $currentPage + 1;
            $str .= "<a class='next' href='" . url(array('pg'=>$nextPage . $queryVars)) . "' title='Next'>&gt;</a> ";
            $str .= "<a class='last' href='" . url(array('pg'=>$parent->fetchNumberPages() . $queryVars)) . "'  title='Last'>&gt;&gt;</a>";
        }
        
        // Wrap in a div
        $pagination = "<div id='pagination'>";
        $pagination .= $str;
        $pagination .= "</div>";
        
        return $pagination;
    }
}
?>