<?php

class EasyAsk_Search_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List {

	protected $_eaProductCollection;
	
    public function getLoadedProductCollection()
    {
        $res = Mage::registry('ea_result');
        $loadProductsFromMagentoDB = Mage::helper('easyask_search')->loadProductsFromMagentoDB();
        $easyask_nav = Mage::getStoreConfig('catalog/navigation/use_easyask');
        
        if ($res) {
            if (is_null($this->_eaProductCollection)) {

				//from parent to populate current category
				$layer = $this->getLayer();
				$origCategory = null;
				if ($res->getSuggestedCategoryID()) {
					$category = Mage::getModel('catalog/category')->load($res->getSuggestedCategoryID());
					if ($category->getId()) {
						$origCategory = $layer->getCurrentCategory();
						$layer->setCurrentCategory($category);
					}
				}

				//from parent
                $last = $res->getLastItem();
                $rpp = $res->getResultsPerPage();
                $pNum = $last / $rpp;
                $offset = ($pNum - 1) * $rpp - 1;
                
                $layer = $this->getLayer();

                if ($loadProductsFromMagentoDB || ($easyask_nav && (get_class($layer) == 'EasyAsk_Search_Model_Catalog_Layer'))) {
                    $eaCollection = $this->_loadDBCollection($res);
                }
                else {
                    $eaCollection = $this->_buildManualCollection($res);
                }

                Mage::dispatchEvent('easyask_search_block_product_list_collection', array(
                    'collection' => $eaCollection
                ));
//                echo get_class($eaCollection);
                
                if ($loadProductsFromMagentoDB || ($easyask_nav && (get_class($layer) == 'EasyAsk_Search_Model_Catalog_Layer'))) {
                	$eaCollection->load();
                	$eaCollection = $this->_sortCollectionAfterLoad($eaCollection, $res);
                }
                else {
                    $eaCollection->setIsLoaded(true);
                }

                echo 'Size of list is' . sizeof($eaCollection);
                $this->_eaProductCollection = $eaCollection;
            }

            Mage::dispatchEvent('easyask_search_block_product_list_collection', array(
                'collection' => $this->_eaProductCollection
            ));

            return $this->_eaProductCollection;

        } else {
            return parent::getLoadedProductCollection();
        }

    }

    public function prepareProductCollection($collection, $res)
    {
        $collection
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setStore(Mage::app()->getStore())
            ->setPageSize($res->getResultsPerPage())
//            ->setCurPage($res->getCurrentPage())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);

        return $this;
    }

    protected function _getProductCollection()
    {
        $res = Mage::registry('ea_result');

        if ($res) {
            return $this->_eaProductCollection;
        } else {
            return parent::_getProductCollection();
        }
    }

    protected function _buildManualCollection($res)
    {
        // Clear the existing list
        $eaLayer = $this->getLayer();
        $eaCollection = $eaLayer->getProductCollection();
        $eaCollection->clear();

        if ($res->getFirstItem() != -1) {
            $last = $res->getLastItem();
            for ($i = $res->getFirstItem() - 1; $i < $last; $i++) {
                $item = $eaCollection->getNewEmptyItem();
                $data = array();

                $regularPrice = $this->_getPriceAmount($res->getCellData($i, $res->getColumnIndex('Regular Price')));
                $price = $this->_getPriceAmount($res->getCellData($i, $res->getColumnIndex('Regular Price')));

                $finalPrice = $this->_getPriceAmount($res->getCellData($i, $res->getColumnIndex('Price')));
                $minPrice = $this->_getPriceAmount($res->getCellData($i,$res->getColumnIndex('Min Price')));
                $maxPrice = $this->_getPriceAmount($res->getCellData($i,$res->getColumnIndex('Price')));

                $data['final_price'] = $finalPrice;
                $data['min_price'] = $minPrice;
                $data['max_price'] = $maxPrice;

                if ((float) $finalPrice < (float) $regularPrice) {
                    $data['price'] = $regularPrice;
                    $data['special_price'] = $finalPrice;
                }
                else {
                    $data['price'] = $price;
                    $data['special_price'] = NULL;
                }
                
                $productName;
                $productImage;
                
                $numberInGroup = $res->getCellData($i, $res->getColumnIndex('No. products in group'));
                $productName = $res->getCellData($i, $res->getColumnIndex('Product Name'));
                $productImage = $res->getCellData($i, $res->getColumnIndex('Small Image'));
                $skuProductName = $res->getCellData($i, $res->getColumnIndex('SKU Product Name'));
                $skuProductImage = $res->getCellData($i, $res->getColumnIndex('SKU Product Image'));
                
                if (isset($numberInGroup) && $numberInGroup > 1){
                	$data['name'] = $productName;
                	$data['small_image'] = $productImage;
				} else {
                	$data['name'] = $productName;
                	if (isset($skuProductImage)){
	                	$data['small_image'] = $skuProductImage;
                	} else {
	                	$data['small_image'] = $productImage;
                	}
                }

                $data['entity_id'] = $res->getCellData($i, $res->getColumnIndex('Product Id'));
                $data['sku'] = $res->getCellData($i, $res->getColumnIndex('Sku'));
                $data['url_key'] = $res->getCellData($i, $res->getColumnIndex('Small Image'));
                $data['thumbnail'] = $res->getCellData($i, $res->getColumnIndex('Thumbnail URL'));
                $data['type_id'] = $res->getCellData($i, $res->getColumnIndex('Type Id'));
                $data['enable_category_checkout'] = (bool) $res->getCellData($i, $res->getColumnIndex('Enable Category Checkout'));
                $data['dynamic_image'] = $res->getCellData($i, $res->getColumnIndex('Dynamic Image'));
                $data['intro_date'] = $res->getCellData($i, $res->getColumnIndex('Intro Date'));
                $data['repeat_image'] = $res->getCellData($i, $res->getColumnIndex('Repeat Image'));
                $data['application_image'] = $res->getCellData($i, $res->getColumnIndex('Application Image'));
                $data['short_description'] = $res->getCellData($i, $res->getColumnIndex('Short Description'));
                $data['status'] = '1';
                $data['request_path'] = $res->getCellData($i, $res->getColumnIndex('Url Path'));
                $data['is_salable'] = '1';
                
                $item->addData($data);
                $eaCollection->addItem($item);
            }
        }

        return $eaCollection;
    }

    
    protected function _loadDBCollection($res)
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        if ($res->getFirstItem() != -1) {
            $productIds = array();
            $last = $res->getLastItem();
            for ($i = $res->getFirstItem() - 1; $i < $last; $i++) {
                $productIds[] = (int) $res->getCellData($i, $res->getColumnIndex('Product Id'));
            }
            $collection->addAttributeToFilter('entity_id', array('in' => $productIds));
            $this->prepareProductCollection($collection, $res);
        }
        return $collection;
    }
    
     /**
     * Return price without currency sent by EasyAsk
     *
     * @param string $price price including currency symbol
     *
     * @return string
     */
     protected function _getPriceAmount($price)
    {
    	$pattern = "/([0-9,]+\.?[0-9]+)/";
    	if (preg_match($pattern, $price, $matches)) {
    		return str_replace(',', '', $matches[0]);
    	}
    	return $price;
    }
    
    protected function _sortCollectionAfterLoad($_productCollection, $res){
		$collectionReflection = new ReflectionObject($_productCollection);
		$itemsPropertyReflection = $collectionReflection->getProperty('_items');
		$itemsPropertyReflection->setAccessible(true); // Make it accessible
		
		$collectionItems = $itemsPropertyReflection->getValue($_productCollection);
		
		$collectionItems = $this->_sortItems($collectionItems, $res);
		
		$itemsPropertyReflection->setValue($_productCollection, $collectionItems);
		
		$itemsPropertyReflection->setAccessible(false);
		return $_productCollection;
    }
    
    protected function _sortItems($collection, $res){
    	$sortCollection = array();
    	
    	$productIds = array();
    	$last = $res->getLastItem();
    	for ($i = $res->getFirstItem() - 1; $i < $last; $i++) {
    		$productIds[] = (int) $res->getCellData($i, $res->getColumnIndex('Product Id'));
    	}
    	
    	foreach ($productIds as $productId){
    		if (isset($collection[$productId])){
    			$sortCollection[$productId] = $collection[$productId];
    		}
    	}
    	return $sortCollection;
    }
    
}