<?php
// Contains information about the current product items.
class EasyAsk_Impl_ItemDescriptions{

	private $m_isDrillDownActive = false;
	private $m_pageCount = -1;
	private $m_currentPage = -1;
	private $m_totalItems = -1;
	private $m_resultsPerPage = -1;
	private $m_firstItem = -1;
	private $m_lastItem = -1;
	private $m_sortOrder = "";
	private $m_descs = null;

	// Makes a new list of item descriptions and populates from an xml node
	function __construct($node){
		if (!$node){
			$this->m_descs = array();
		}else{
            $this->m_isDrillDownActive = $node->isDrillDownActive;
            $this->m_pageCount = $node->pageCount;
            $this->m_currentPage = $node->currentPage;
            $this->m_totalItems = $node->totalItems;
            $this->m_resultsPerPage = $node->resultsPerPage;
            $this->m_firstItem = $node->firstItem;
            $this->m_lastItem = $node->lastItem;
            $this->m_sortOrder = isset($node->sortOrder)? $node->sortOrder : '';

            $itemDescs = $node->dataDescription;
			$this->m_descs = array();
			foreach ($itemDescs as $desc){
                $this->m_descs[] = new EasyAsk_Impl_DataDescription($desc);
            }
        }
	}
		
	function getIsDrillDown() { return $this->m_isDrillDownActive; }
	
	// Returns the total number of results pages
	function getPageCount() { return $this->m_pageCount; }
	
	// Returns the current displayed result page
	function getCurrentPage() { return $this->m_currentPage; }
	
	// Returns the total number of product items
	function getTotalItems() { return $this->m_totalItems; }
	
	// Returns the current number of items per page
	function getResultsPerPage() { return $this->m_resultsPerPage; }
	
	// Returns the index of the first item on the current results page
	function getFirstItem() { return $this->m_firstItem; }
	
	// Returns the index of the last item on the current results page
	function getLastItem() { return $this->m_lastItem; }
	
	// Returns the sort order currently emplyed by the results
	function getSortOrder() { return $this->m_sortOrder; }
	
	// Returns a list containing the descriptions for all the items
	function getDataDescriptions() { return $this->m_descs; }
	
	// Returns the index of a specific item description within the list
    // Returns -1 if the item description is not found
	function getColumnIndex($colName){
		for ($i = 0; $i < count($this->m_descs); $i++){
			$desc = $this->m_descs[$i];
			if (0 == strcmp($colName, $desc->getColName())){
				return $i;
			}
		}
        return -1;
	}
}
?>