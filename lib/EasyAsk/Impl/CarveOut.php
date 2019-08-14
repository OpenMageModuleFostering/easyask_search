<?php
// Represents a Search Advisor Carve Out - a subset of the current result set that share the same attribute.
class EasyAsk_Impl_CarveOut implements EasyAsk_ICarveOut
{
	private $m_node = null;
	private $m_res = null;
	
	private $m_items = null;
	private $m_maximum = -1;
	private $m_displayFormat = null;
	private $m_productCount = -1;
	
	function __construct($res, $node){
		$this->m_node = $node;
		$this->m_res = $res;
		$this->m_maximum = $node->maximum;
		$this->m_displayFormat = $node->displayFormat;
		$this->m_productCount = count($node->items); 
		$this->processItems();
	}
	
	private function processItems(){
		if ($this->m_items == null){
			$this->m_items = array();
			foreach ($this->m_node->items as $item){
				$this->m_items[] = new EasyAsk_Impl_ItemRow($this->m_res->getDataDescriptions(), $item);
			}
		}
	}
	
	public function getMaximum(){
		return $this->m_maximum;
	}
	
	public function getDisplayFormat(){
		return $this->m_displayFormat;
	}
	
	public function  getItems(){
		return $this->m_items;
	}
	
	public function getProductCount(){
		return $this->m_productCount;
	}
}

?>