<?php

require_once "PageLayout.php";

class DoubleBarLayout implements PageLayout {

	public function fetchPagedLinks($parent, $queryVars) {
	
		$currentPage = $parent->getPageNumber();
		$str = "";

		if(!$parent->isFirstPage()) {
			if($currentPage != 1 && $currentPage != 2 && $currentPage != 3) {
				$str .= "<a href='" . url(array('pg'=>'1' . $queryVars)) . "'  title='Start'>First </a>";
			}
		}

		//write statement that handles the previous and next phases
	   	//if it is not the first page then write previous to the screen
		if(!$parent->isFirstPage()) {
			$previousPage = $currentPage - 1;
			$str .= "<a href='" . url(array('pg'=>$previousPage . $queryVars)) . "'>&lt; previous </a>";
		}

		for($i = $currentPage - 2; $i <= $currentPage + 2; $i++) {
			//if i is less than one then continue to next iteration		
			if($i < 1) {
				continue;
			}
	
			if($i > $parent->fetchNumberPages()) {
				break;
			}
	
			if($i == $currentPage) {
				$str .= "<i>Page $i</i>";
			}
			else {
				$str .= "<a href='" . url(array('pg'=>$i . $queryVars)) . "'>$i</a>";
			}
			($i == $currentPage + 2 || $i == $parent->fetchNumberPages()) ? $str .= " " : $str .= " | ";              //determine if to print bars or not
		} //end for

		if (!$parent->isLastPage()) {
			if($currentPage != $parent->fetchNumberPages() && $currentPage != $parent->fetchNumberPages() -1 && $currentPage != $parent->fetchNumberPages() - 2)
			{
				$str .= " &gt; <a href=\"?pg=".$parent->fetchNumberPages()."$queryVars\" title=\"Last\">Last(".$parent->fetchNumberPages().") </a>";
				$str .= " &gt; <a href='" . url(array('pg'=>$parent->fetchNumberPages() . $queryVars)) . "'  title='Last'>Last(".$parent->fetchNumberPages().")</a>";
			}
		}

		if(!$parent->isLastPage()) {
			$nextPage = $currentPage + 1;
			$str .= "<a href='" . url(array('pg'=>$nextPage . $queryVars)) . "'>next &gt;</a>";
		}
		return $str;
	}
}
?>