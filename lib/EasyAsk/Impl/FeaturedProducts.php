<?php
// Contains the featured products
class EasyAsk_Impl_FeaturedProducts implements EasyAsk_iFeaturedProducts
{
	private $m_node = null;
	private $m_res = null;
	private $m_items = null;
	private $m_productCount = -1;
	
	// Builds a featured items instance off of an appropriate xml node
	function __construct($res, $node){
		$this->m_node = $node;
		$this->m_res = $res;
		if ($this->m_node != null){
			$this->m_productCount = $node->productCount;
		}
	}
	
	// Adds all featured products contained within the xml node to a list.
	// There will be no data in this instance until this method is run.
	private function processItems(){
		if ($this->m_items == null){
			$this->m_items = array();
			if($this->m_node != null){
				foreach ($this->m_node->item as $item){
					$this->m_items[] = new EasyAsk_Impl_ItemRow($this->m_res->getDataDescriptions(), $item);
				}
			}
		}
	}
	
	// Returns the list of featured items
	public function getItems(){
		$this->processItems();
		return $this->m_items;
	}
	
	// Returns a count of the current featured products.
	public function getProductCount(){
		return $this->m_productCount;
	}
}
?>