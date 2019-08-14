<?php
class EasyAsk_Search_Model_Resource_Catalog_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection{
	public function getSize()
	    {
		$res = Mage::registry('ea_result');
		if ($res != null){
			$size = $res->getResultCount();
			return $size != -1? $size : 0;
		}else{
			return parent::getSize();
		}
	}
	
	public function setIsLoaded($flag = true)
	{
		return $this->isLoaded();
	}
}