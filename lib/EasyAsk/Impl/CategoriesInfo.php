<?php
// Contains a list of EasyAsk categories and provides methods to easily access
// the categories and pertaining data for the current search as well as the intial values.
class EasyAsk_Impl_CategoriesInfo
{
	private $m_node = null;
	private $m_categories = array();
	private $m_initialCategories = array();
	private $m_suggestedCategoryTitle = "Categories";
	private $m_suggestedCategoryID = "";
	private $m_detailedSuggestedCategory = "Categories";
	private $m_detailedSuggestedProductCount = -1;
	private $m_detailedSuggestedIDs = "";
	private $m_detailedSuggestedNodeString = "";
	private $m_detailedSuggestedSEOPath = "";
	private $m_initialDisplayLimitForCategories = -1;

	// Builds the category info off of a category node
	function __construct($node){
		$this->m_node = $node;
		$this->processCategories();
	}

	// Processes the category node and adds the node to a list of existing categories
	// As well as maintains a list of initial categories for when the user is on the main page.
	private function processCategories(){
		if ($this->m_node != null){
			$catNode = $this->m_node->categories;
			if ($catNode != null){
				$temp = $catNode->suggestedCategoryTitle;
				if ($temp != null){
					$this->m_suggestedCategoryTitle = $temp;
				}
				$temp = $catNode->suggestedCategoryID;
				if ($temp != null){
					$this->m_suggestedCategoryID = $temp;
				}
				$temp = isset($catNode->detailedSuggestedCategory)?$catNode->detailedSuggestedCategory:null;
				if ($temp != null){
					$this->m_detailedSuggestedCategory = $temp;
					$this->m_detailedSuggestedProductCount = $temp->productCount;
					$this->m_detailedSuggestedIDs = $temp->ids;
					$this->m_detailedSuggestedNodeString = $temp->nodeString;
					$this->m_detailedSuggestedSEOPath = $temp->seoPath;
				}
				$cats = isset($catNode->categoryList)?$catNode->categoryList:null;
				if ($cats != null && sizeof($cats) > 0){
					foreach ($cats as $cat){
						$this->m_categories[] = new EasyAsk_Impl_NavigateCategory($cat);
					}
				}
				$cats = isset($catNode->initialCategoryList)?$catNode->initialCategoryList:null;
				if ($cats != null && sizeof($cats) > 0){
					$this->m_initialDisplayLimitForCategories = $catNode->InitDispLimit;
					foreach ($cats as $cat){
						$this->m_initialCategories[] = new EasyAsk_Impl_NavigateCategory($cat);
					}
				}
			}
		}
	}

	// Returns a list of categories nodes.
	// Will return the initial list of nodes if the Displaymode is still initial.
	// Otherwise, will return the current list of categories.
	public function getDetailedCategories($nDisplayMode){
		if ($nDisplayMode == 1){
			return $this->m_initialCategories;
		}else{
			return $this->m_categories;
		}
	}

	// Gets a list of the current category nodes
	public function getDetailedCategoriesFull(){
		return $this->getDetailedCategories(0);
	}

	// Returns a suggested category title based off of parent nodes
	public function getSuggestedCategoryTitle(){
		return $this->m_suggestedCategoryTitle;
	}

	// Returns a suggested category ID based off of parent nodes
	public function getSuggestedCategoryID(){
		return $this->m_suggestedCategoryID;
	}

	// Returns a detailed suggested category based off of parent nodes
	public function getDetailedSuggestedCategory(){
		return $this->m_detailedSuggestedCategory;
	}

	// Returns a detailed suggested category count based off of parent nodes
	public function getDetailedSuggestedProductCount(){
		return $this->m_detailedSuggestedProductCount;
	}

	// Returns a detailed suggested category IDs based off of parent nodes
	public function getDetailedSuggestedIDs(){
		return $this->m_detailedSuggestedIDs;
	}

	// Returns a detailed suggested category node string based off of parent nodes
	public function getDetailedSuggestedNodeString(){
		return $this->m_detailedSuggestedNodeString;
	}

	// Returns a detailed suggested Search Enginer Optimization path based off of parent nodes
	public function getDetailedSuggestedSEOPath(){
		return $this->m_detailedSuggestedSEOPath;
	}

	// Returns the display limit for initial list
	public function getInitDisplayLimitForCategories(){
		return $this->m_initialDisplayLimitForCategories;	
	}
	
}
?>