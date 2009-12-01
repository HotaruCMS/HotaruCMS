<?php
/**
 * The intention of the Paginated class is to manage the iteration of records
 * based on a specified page number usually addressed by a get parameter in the query string
 * and to use a layout interface to produce number pages based on the amount of elements
 */

require_once "PageLayout.php";

class Paginated {

	private $rs;                  		//result set
	private $pageSize;                      //number of records to display
	private $pageNumber;                    //the page to be displayed
	private $rowNumber;                     //the current row of data which must be less than the pageSize in keeping with the specified size
	private $offSet;
	private $layout;

	function __construct($obj, $displayRows = 10, $pageNum = 1) {
		$this->setRs($obj);
		$this->setPageSize($displayRows);
		$this->assignPageNumber($pageNum);
		$this->setRowNumber(0);
		$this->setOffSet(($this->getPageNumber() - 1) * ($this->getPageSize()));
	}

	//implement getters and setters
	public function setOffSet($offSet) {
		$this->offSet = $offSet;
	}

	public function getOffSet() {
		return $this->offSet;
	}


	public function getRs() {
		return $this->rs;
	}

	public function setRs($obj) {
		$this->rs = $obj;
	}

	public function getPageSize() {
		return $this->pageSize;
	}

	public function setPageSize($pages) {
		$this->pageSize = $pages;
	}

	//accessor and mutator for page numbers
	public function getPageNumber() {
		return $this->pageNumber;
	}

	public function setPageNumber($number) {
		$this->pageNumber = $number;
	}

	//fetches the row number
	public function getRowNumber() {
		return $this->rowNumber;
	}

	public function setRowNumber($number) {
		$this->rowNumber = $number;
	}

	public function fetchNumberPages() {
		if (!$this->getRs()) {
			return false;
		}
		
		$pages = ceil(count($this->getRs()) / (float)$this->getPageSize());
		return $pages;
	}

	//sets the current page being viewed to the value of the parameter
	public function assignPageNumber($page) {
		if(($page <= 0) || ($page > $this->fetchNumberPages()) || ($page == "")) {
			$this->setPageNumber(1);
		}
		else {
			$this->setPageNumber($page);
		}
		//upon assigning the current page, move the cursor in the result set to (page number minus one) multiply by the page size
		//example  (2 - 1) * 10
	}

	public function fetchPagedRow() {
		if((!$this->getRs()) || ($this->getRowNumber() >= $this->getPageSize())) {
			return false;
		}

		$this->setRowNumber($this->getRowNumber() + 1);
		$index = $this->getOffSet();
		$this->setOffSet($this->getOffSet() + 1);
		// return $this->rs[$index];
		if(isset($this->rs[$index])) { return $this->rs[$index]; } else { return false; } // edited by Nick to suppress undefined error.
	}

	public function isFirstPage() {
		return ($this->getPageNumber() <= 1);
	}

	public function isLastPage() {
		return ($this->getPageNumber() >= $this->fetchNumberPages());
	}

	/**
	 * <description>
	 * @return PageLayout <description>
	 */
	public function getLayout() {
		return $this->layout;
	}

	/**
	 * <description>
	 * @param PageLayout <description>
	 */
	public function setLayout(PageLayout $layout) {
		$this->layout = $layout;
	}

	//returns a string with the base navigation for the page
	//if queryVars are to be added then the first parameter should be preceeded by a ampersand
	public function fetchPagedNavigation($queryVars = "", $hotaru = NULL) {
		return $this->getLayout()->fetchPagedLinks($this, $queryVars, $hotaru);
	}//end writeNavigation
}//end Paginated
?>