<?php
class EasyAsk_Search_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Returns generated Products CSV file
	 */
	public function productsListAction()
	{
		$filename = 'eae_products.csv';
		$content = Mage::helper('easyask_search/eae')->generateEaeProductsList();

		$this->_prepareDownloadResponse($filename, $content);
	}
	
	/**
	 * Returns generated Catagory CSV file
	 */
	public function categoryListAction()
	{
		$filename = 'eae_categories.csv';
		$content = Mage::helper('easyask_search/eae')->generateEaeCategoriesList();
	
		$this->_prepareDownloadResponse($filename, $content);
	}
	
	/**
	 * Returns generated Attribute CSV file
	 */
	public function attributeListAction()
	{
		$filename = 'eae_attributes.csv';
		$content = Mage::helper('easyask_search/eae')->generateEaeAttributesList();
	
		$this->_prepareDownloadResponse($filename, $content);
	}
	
	/**
	 * Returns generated Product Attribute CSV file
	 */
	public function productAttributeListAction()
	{
		$filename = 'eae_productAttributes.csv';
		$content = Mage::helper('easyask_search/eae')->generateEaeProductAttributesList();
	
		$this->_prepareDownloadResponse($filename, $content);
	}
	
	/**
	 * Returns generated Category Products CSV file
	 */
	public function categoryProductsListAction()
	{
		$filename = 'eae_categoryProducts.csv';
		$content = Mage::helper('easyask_search/eae')->generateEaeCategoryProductsList();

		$this->_prepareDownloadResponse($filename, $content);
	}
	
}