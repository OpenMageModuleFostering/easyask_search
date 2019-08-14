<?php
// Contains product data in columns. Used to display products.
class EasyAsk_Impl_ItemRow implements EasyAsk_iResultRow
{
	private $m_items;

	// Creates the ItemRow
	function __construct($desc, $item)
	{
		$this->m_items = array();
		foreach($desc as $dd){
			$tagname = $dd->getTagName();
			$val = isset($item->$tagname)?$item->$tagname:'';
			if ($val){
				$this->m_items[] = $val;
			}else{
				$this->m_items[] = "";
			}
		}
	}

	// Returns the data contained in a specific column
	function getFormattedText($col)
	{
		return $col >= 0?(String)$this->m_items[$col]:'';
	}
	
	// Returns the data contained in a specific column
	public function getCellData($col){
		return $this->m_items[$col];
	}
	
	// Returns the amount of columns contained within the row
	public function size(){
		return sizeof($this->m_items);
	}
}
?>