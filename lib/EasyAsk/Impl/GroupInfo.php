<?php
// Containts attribute and category information for a certain node.
class EasyAsk_Impl_GroupInfo implements EasyAsk_iGroupedResult
{
	private $m_node = null;
	private $m_res = null;
	private $m_name = null;
	private $m_count;
	private $m_totalRows = 0;
	private $m_items;
	private $m_catInfo = null;
	private $m_attributes = null;
	private $m_set = null;
	
	function __construct($res, $node, $set){
		$this->m_res = $res;
		$this->m_node = $node;
		$this->m_set = $set;
		$this->m_name = $node->name;
		$this->m_count = $node->productCount;
		$this->m_totalRows = $node->totalRows;
	}
	
	// Creates a CategoriesInfo instance off of the xml node.
	private function processCategories(){
		if (null == $this->m_catInfo){
			$this->m_catInfo = new EasyAsk_Impl_CategoriesInfo($this->m_node);
		}
	}
	
	// Creates an AttributeInfo list off of the xml node.
	private function processAttributes(){
		if (null == $this->m_attributes){
			$attrNodes = $this->m_node->attribute;
			$this->m_attributes = array();
			foreach ($attrNodes as $attr){
				$this->m_attributes[] = new EasyAsk_Impl_AttributeInfo($attr);	
			}
		}
	}
	
	// Creates a list of ItemRows based off of the nodes relative to the node provided in the constructor.
	private function processItems(){
		if (null == $this->m_items){
			$this->m_items = array();
			$items = $this->m_node->item;
			if ($items != null){
				$dd = $this->m_res->getDataDescriptions();
				foreach ($items as $item){
					$this->m_items[] = new EasyAsk_Impl_ItemRow($dd, $item);
				}
			}
		}
	}
	
	// Returns a list of NavigateCategories made from the CategoryInfo instance for the node.
	protected function getDetailedCategories($nDisplayMode){
		$this->processCategories();
		return $this->m_catInfo->getDetailedCategories($nDisplayMode);
	}
	
	// Returns the list of all NavigateCategories made from instance for this node
	public function getAllCategoryDetails(){
		return $this->getDetailedCategories(0);
	}
	
	// Returns a list of category names.
	public function getCategories(){
		$result = array();
		foreach ($this->getAllCategoryDetails() as $navCat){
			$result[] = $navCat['content'];
		}
		return $result;
	}
	
	// Returns the suggested category title created by the CategoryInfo instance.
	public function getSuggestedCategoryTitle(){
		$this->processCategories();
		return $this->m_catInfo->getSuggestedCategoryTitle();
	}
	
	// Returns a list of attribute names.
	public function getAttributeNames(){
		$this->processAttributes();
		$result = array();
		foreach ($this->m_attributes as $info){
			$result[] = $info['content'];
		}
		return $result;
	}
	
	// Returnes the AttributeInfo instance for a certain attribute.
    // Returns null if this attribute is not contained within this instance.
	private function getAttrInfo($attrName){
		$this->processAttributes();
		foreach($this->m_attributes as $attrInfo){
			if(0 == strcmp($attrName, $attrInfo['content'])){
				return $attrInfo;
			}
		}
		return null;
	}
	
	// Returns a list of NavigateAttributes from the xml node.
	public function getDetailedAttributeValues($attrName, $displayMode){
		$attrInfo = $this->getAttrInfo($attrName);
		if (null != $attrInfo){
			if ($displayMode == 1 && !$attrInfo->getIsLimited() ){
				$displayMode = 0;
			}
			return $displayMode == 0 ? $attrInfo->getFullList() : $attrInfo->getInitialList();
		}
		return array();
	}
	
	// Returns the number of ItemRows contained within this GroupInfo
	public function getNumberOfRows(){
		$this->processItems();
		return sizeof($this->m_items);
	}
	
	// Returns the total number of rows for the GroupInfo
	public function getTotalNumberOfRows(){
		return $this->m_totalRows;
	}
	
	public function getSuggestedCategoryID(){
		return ""; //todo
	}
	
	public function getDetailedSuggestedCategory(){
		return null; //todo
	}
	
	// Returns the count of items that match a certain attribute value.
	public function getAttributeValueCount($attr, $val){
		$this->processAttributes();
		foreach ($this->getDetailedAttributeValues($attr, 0) as $navAttr){
			if (strcmp($navAttr->getValue,$val) == 0){
				return $navAttr->getProductCount();
			}
		}
		return -1;
	}
	
	// Returns the cell data from a specific location in the GroupInfo.
	public function getCellData($row, $col){
		$this->processItems();
		return $this->m_items[$row]->getFormattedText($col);
	}
	
	// Returns the index of the last row in the current page.
	public function getEndRow(){
		return $this->m_set->getGroupEndRow($this);
	}
	
	// Returns the index of the first row in the current page.
	public function getStartRow(){
		return $this->m_set->getGroupStartRow($this);
	}
	
	// Returns the name of this GroupInfo.
	public function getGroupValue(){
		return $this->m_name;
	}
	
	// Returns the ResultRow at the specified index.
	public function getItem($row){
		$this->processItems();
		return $this->m_items[$row];
	}
}
?>