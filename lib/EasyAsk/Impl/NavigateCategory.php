<?php
// Represents a search advisor category
class EasyAsk_Impl_NavigateCategory implements EasyAsk_iNavigateCategory
{
	private $m_productCount = -1;
	private $m_name;
	private $m_ids = null;
	private $m_subCategories = null;
	private $m_nodeString = null;
	private $m_seoPath = null;

	// Generates the Navigate Category based off of a category xml node
	function __construct($def){
		$this->m_name = $def->name;
		$this->m_productCount = $def->productCount;
		$this->m_nodeString = $def->nodeString;
		$this->m_seoPath = $def->seoPath;
		
		$attrIDs = $def->ids;
		if (null != $attrIDs){
			$ids = explode(",", $attrIDs);
			$this->m_ids = array();
			foreach($ids as $id){
				$this->m_ids[] = $id;
			}
		}
		$subCats = isset($def->subCategories->category)?$def->subCategories->category:null;
		if (null != $subCats){
			$this->m_subCategories = array();
			foreach($subCats as $sub)
			{
				$this->m_subCategories[] = new EasyAsk_Impl_NavigateCategory($sub);
			}
		}
	}

	// Returns the category name
	public function getName() { return $this->m_name; }
	
	// Returns the total number of products that have this category. Returns -1 if this is unkown.
	public function getProductCount() { return $this->m_productCount; }
	
	// returns a list of ids (as Strings) that correspond to the category named in this object. 
	//The list will contain at least 1 entry and possibly more if there are multiple categories 
	//with the same name at the same level. The ids will be only from categories that are referenced by 
	//products in the result set, that is, if there are 5 categories with the same name, but only 3 of them are 
	//referenced in the result set, then the list will contain 3 Strings not 5
	public function getIDs() { return $this->m_ids; }
	
	// Returns a list of NavigateCategory corresponding to the subcategories of this node
    // (if any exist and the AdvisorOptions requested that the be generated) or null.
	public function getSubCategories() { return $this->m_subCategories; }
	
	// Returns a string corresponding to the path segment for this category. See NavigateNode.toString()
	public function getNodeString(){
		return $this->m_nodeString;
	}
	
	// Returns a string corresponding to the full path to this category
	public function getSEOPath(){
		return $this->m_seoPath;
	}
}
?>