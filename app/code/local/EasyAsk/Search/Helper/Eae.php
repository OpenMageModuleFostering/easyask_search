<?php
ini_set('memory_limit', '1024M');

class EasyAsk_Search_Helper_Eae extends Mage_Core_Helper_Abstract
{
	/**
	 * Contains current category collection
	 * @var string
	 */
	protected $_categoryList = null;
	
	/**
	 * Contains current attribute collection
	 * @var string
	 */
	protected $_attributeList = null;
	protected $_selectableAttributeList = null;
	protected $_multiselectableAttributeList = null;
	protected $_selectableAttributes = null;
	protected $_multiselectableAttributes = null;
	protected $_config = null;
	
	public function __construct()
	{
		
		$atts = $this->_getXmlConfig()->getXpath('field');
		
		//Categories
		$category = Mage::getModel ( 'catalog/category' );
		$tree = $category->getTreeModel ();
		$tree->load ();
		
		$this->setCategoryList($tree->getCollection ()->getAllIds());
		
//		$tempAttribs = null;
		$attributes = null;
		foreach ($atts as $attr){
			$attributes[] = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attr->asArray());
		}
		
		//Attributes
//		$attributes = Mage::getResourceModel ( 'catalog/product_attribute_collection' )->getItems();
		$this->setAttributeList($attributes);
		
	}

	/**
	 * Sets current collection
	 * @param $query
	 */
	public function setCategoryList($collection)
	{
		$this->_categoryList = $collection;
	}

	/**
	 * Sets current collection
	 * @param $query
	 */
	public function setAttributeList($collection)
	{
		$this->_attributeList = $collection;
		
		
		//make a subset of selectable attributes
		foreach ($collection as $attribute){
			$attribData = $attribute->getData(); 
			if ($attribData['frontend_input'] == 'select'){
				$this->_selectableAttributeList[] = $attribData;
				$this->_selectableAttributes[$attribData['attribute_code']] = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribData['attribute_code'])->getSource()->getAllOptions(true); 
			}
			if ($attribData['frontend_input'] == 'multiselect'){
				$this->_multiselectableAttributeList[] = $attribData;
				$this->_multiselectableAttributes[$attribData['attribute_code']] = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribData['attribute_code'])->getSource()->getAllOptions(true); 
			}
		}
	}

	/**
	 * Returns indexes of the fetched array as headers for CSV
	 * @param array $products
	 * @return array
	 */
	protected function _getCsvHeaders($product)
	{
		$headers = array();
		
		$keys = array_keys($product->getData());
		foreach($keys as $key){
			if (!in_array($key, $headers) && ($key != 'stock_item')){
				$headers[] = $key;
			}
		}
				
		foreach ($this->_attributeList as $attribute){
			$attrCode = $attribute['attribute_code'];
			if (!in_array($attrCode, $headers) && ($attrCode != 'stock_item')){
				$headers[] = $attrCode;
			}
		}
		
		$headers[] = 'qty';
		$headers[] = 'is_in_stock';
		$headers[] = 'parent_ids';

		return $headers;
	}

	/**
	 * Returns indexes of the fetched array as headers for CSV
	 * @param array $ids
	 * @return array
	 */
	protected function _getCatCsvHeaders($ids)
	{
		$cat = Mage::getModel('catalog/category');
		$cat->load($ids[sizeof($ids) - 1]);
		
		$headers = array_keys($cat->getData());

		return $headers;
	}

	/**
	 * Returns indexes of the fetched array as headers for CSV
	 * @param array $attributes
	 * @return array
	 */
	protected function _getAttrCsvHeaders($attributes)
	{
		$attribute = current($attributes);
		$headers = array_keys($attribute->getData());

		return $headers;
	}

	/**
	 * Returns indexes of the fetched array as data for CSV
	 * @param array $products
	 * @return array
	 */
	protected function _getCsvData($rec, $headers)
	{
		$data = array();
		foreach ($headers as $header){
			$data[$header] = isset($rec[$header]) ? $rec[$header] : '';
		}

		return $data;
	}
	
	protected function _transformProductData($product)
	{
		$selectAttributes = $this->_selectableAttributeList;
		$multiSelectAttributes = $this->_multiselectableAttributeList;
		foreach ($selectAttributes as $selectAttribute){
			if (isset($product[$selectAttribute['attribute_code']])){
				$attributeOptions = $this->_selectableAttributes[$selectAttribute['attribute_code']];   
				foreach ($attributeOptions as $option) {
					if ($option['value'] == $product[$selectAttribute['attribute_code']]){
						$product[$selectAttribute['attribute_code']] = $option['label'];
					}
				}
			}
		}
		foreach ($multiSelectAttributes as $multiSelectAttribute){
			if (isset($product[$multiSelectAttribute['attribute_code']])){
				$multiAttributes = explode(",", $product[$multiSelectAttribute['attribute_code']]);
				$multiAttributeLabels = array();
				$attributeOptions = $this->_multiselectableAttributes[$multiSelectAttribute['attribute_code']];

				foreach ($multiAttributes as $multiAttribute){
					foreach ($attributeOptions as $option) {
						if ($option['value'] == $multiAttribute){
							$multiAttributeLabels[] = $option['label'];
						}
					}
				}
				$product[$multiSelectAttribute['attribute_code']] = implode(",", $multiAttributeLabels);
			}
		}
		return $product;	
	}

	/**
	 * Generates CSV file with product's list according to the collection in the $this->_productList
	 * @return array
	 */
	public function generateEaeProductsList()
	{
		$prodcollection = Mage::getModel('catalog/product')->getCollection()->setPageSize(1);//->addAttributeToSelect('*');
		
		$count = $prodcollection->getSize();
		
		$io = new Varien_Io_File();
		$path = Mage::getBaseDir('var') . DS . 'export' . DS;
		$name = md5(microtime());
		$file = $path . DS . $name . '.csv';
		$io->setAllowCreateFolders(true);
		$io->open(array('path' => $path));
		$io->streamOpen($file, 'w+');
		$io->streamLock(true);
		
		$page = 1;
		$pageSize = 200;
		$headers = array();
		
		foreach ($prodcollection as $prod){
			$headers = $this->_getCsvHeaders($prod);
		}
		
		while ($count > ($pageSize * ($page - 1))){
			$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->setPageSize($pageSize)->setCurPage($page);

			if (!is_null($collection)) {
				$i = 0;
				foreach ($collection as $product) {
					
					if (($i == 0) && ($page == 1)){
						$io->streamWriteCsv($headers);
					}
					
					$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
					$proddata = $product->getData();
					$proddata = $this->_transformProductData($proddata);
					unset($proddata['stock_item']);
					$proddata['qty'] = $stockItem->getQty();
					$proddata['is_in_stock'] = $stockItem->getIsInStock;
					$proddata['parent_ids'] = $this->_getParentIds($product);
					$data = $this->_getCsvData($proddata, $headers);
					$io->streamWriteCsv($data);
					$i++;
				}
			}
			$page++;
		}
		return array(
				'type'  => 'filename',
				'value' => $file,
				'rm'    => false // can delete file after use
		);
		
	}

	/**
	 * Generates CSV file with category list according to the collection in the $this->_categoryList
	 * @return array
	 */
	public function generateEaeCategoriesList()
	{
		
		if (!is_null($this->_categoryList)) {
			$ids = $this->_categoryList;
			if (count($ids) > 0) {
	
				$io = new Varien_Io_File();
				$path = Mage::getBaseDir('var') . DS . 'export' . DS;
				$name = md5(microtime());
				$file = $path . DS . $name . '.csv';
				$io->setAllowCreateFolders(true);
				$io->open(array('path' => $path));
				$io->streamOpen($file, 'w+');
				$io->streamLock(true);
	
				$headers = $this->_getCatCsvHeaders($ids);
				$io->streamWriteCsv($headers);
				foreach ($ids as $id) {
					
					$cat = Mage::getModel('catalog/category');
					$cat->load($id);
					
					$data = $this->_getCsvData($cat->getData(), $headers);
						
					$io->streamWriteCsv($data);
				}
	
				return array(
						'type'  => 'filename',
						'value' => $file,
						'rm'    => false // can delete file after use
				);
			}
		}
	}
	
	/**
	 * Generates CSV file with attribute list according to the collection in the $this->_attributeList
	 * @return array
	 */
	public function generateEaeAttributesList()
	{
		
		if (!is_null($this->_attributeList)) {
			$attributes = $this->_attributeList;
			if (count($attributes) > 0) {
	
				$io = new Varien_Io_File();
				$path = Mage::getBaseDir('var') . DS . 'export' . DS;
				$name = md5(microtime());
				$file = $path . DS . $name . '.csv';
				$io->setAllowCreateFolders(true);
				$io->open(array('path' => $path));
				$io->streamOpen($file, 'w+');
				$io->streamLock(true);
	
				$io->streamWriteCsv($this->_getAttrCsvHeaders($attributes));
				foreach ($attributes as $attribute) {
					$attrdata = $attribute->getData();
					$io->streamWriteCsv($attrdata);
				}

				return array(
						'type'  => 'filename',
						'value' => $file,
						'rm'    => false // can delete file after use
				);
			}
		}
	}
	
	/**
	 * Generates CSV file with category products list according to the collection in the $this->_categoryList
	 * @return array
	 */
	public function generateEaeCategoryProductsList()
	{
		
		if (!is_null($this->_categoryList)) {
			$ids = $this->_categoryList;
			if (count($ids) > 0) {
	
				$io = new Varien_Io_File();
				$path = Mage::getBaseDir('var') . DS . 'export' . DS;
				$name = md5(microtime());
				$file = $path . DS . $name . '.csv';
				$io->setAllowCreateFolders(true);
				$io->open(array('path' => $path));
				$io->streamOpen($file, 'w+');
				$io->streamLock(true);

				$headers = array('category_id', 'product_id');
				$io->streamWriteCsv($headers);
				foreach ($ids as $id) {
					
					$cat = Mage::getModel('catalog/category');
					$cat->load($id);
					
					$products = Mage::getModel('catalog/product')
					->getCollection()
					->addCategoryFilter($cat)
					->load();
					
					if (!is_null($products)) {
						foreach ($products as $product) {
							$catProduct = array();

							$catProduct['category_id'] = $cat->getEntityId();

							$proddata = $product->getData();
							$catProduct['product_id'] = $proddata['entity_id'];
											
							$data = $this->_getCsvData($catProduct, $headers);
							$io->streamWriteCsv($data);
						}
					}
				}
	
				return array(
						'type'  => 'filename',
						'value' => $file,
						'rm'    => false // can delete file after use
				);
			}
		}
	}
	
	protected function _getParentIds($product){
		$parentIds = array();
		$parentIds = Mage::getModel('catalog/product_type_configurable')
                            ->getParentIdsByChild( $product->getId() );
		$parentIds = Mage::getResourceSingleton('bundle/selection')
            ->getParentIdsByChild($childId);
		$parentIds = Mage::getModel('catalog/product_type_virtual')
                            ->getParentIdsByChild( $product->getId() );
		return implode(",", $parentIds);
	}
	
	/**
	 * Load config from files and try to cache it
	 *
	 * @return Varien_Simplexml_Config
	 */
	protected function _getXmlConfig()
	{
		if (is_null($this->_config)) {
			$canUseCache = Mage::app()->useCache('config');
			$cachedXml = Mage::app()->loadCache('attrib_config');
			if ($canUseCache && $cachedXml) {
				$xmlConfig = new Varien_Simplexml_Config($cachedXml);
			} else {
				
				
				$xmlPath = Mage::getBaseDir().DS.'app'.DS.'code'.DS.'local'.DS.'EasyAsk'.DS.'Search'.DS.'etc'.DS.'attrib.xml';
				$xmlConfig = new Varien_Simplexml_Config($xmlPath);
				
				if ($canUseCache) {
					Mage::app()->saveCache($xmlConfig->getXmlString(), 'attrib_config',
					array(Mage_Core_Model_Config::CACHE_TAG));
				}
			}
			$this->_config = $xmlConfig;
		}
		return $this->_config;
	}
	
}