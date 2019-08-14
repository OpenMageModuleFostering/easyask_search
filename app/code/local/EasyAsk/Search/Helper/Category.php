<?php
class EasyAsk_Search_Helper_Category extends Mage_Catalog_Helper_Category {
	/**
	 * Retrieve category url
	 *
	 * @param   Mage_Catalog_Model_Category $category
	 * @return  string
	 */
	public function getCategoryUrl($category)
	{
    	$easyask_nav = Mage::getStoreConfig('catalog/navigation/use_easyask');
    
		$catUrl = null;
		if ($category instanceof Mage_Catalog_Model_Category) {
			$catUrl = $category->getUrl();
		} else {
			$catUrl = Mage::getModel('catalog/category')
			->setData($category->getData())
			->getUrl();
		}
		
        if ($easyask_nav){
        //Rewrite the url for EasyAsk
			$catUrl = Mage::getBaseUrl().'catalogsearch/result/index/?ea_c='.str_replace('/', '_', substr($catUrl, strlen(Mage::getBaseUrl())));
        }
		return $catUrl;
	}
	
	
}
