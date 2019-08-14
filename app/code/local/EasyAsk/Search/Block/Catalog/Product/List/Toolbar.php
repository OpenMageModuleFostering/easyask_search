<?php

class EasyAsk_Search_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar{

    /**
     * Set collection to pager
     *
     * @param Varien_Data_Collection $collection
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function setCollection($collection)
    {
        $res = Mage::registry('ea_result');

        if ($res != null){
             
            //over write the collection with ea collection
            //		$this->_collection = $collection;
            $this->_collection = $this->getParentBlock()->getLoadedProductCollection();
//            $this->_collection = $this->getLayout()->getBlock('search.result')->getListBlock()->getLoadedProductCollection() ;
            
            $this->_collection->setCurPage($this->getCurrentPage());

            // we need to set pagination only if passed value integer and more that 0
            $limit = (int)$this->getLimit();
            if ($limit) {
                $this->_collection->setPageSize($limit);
            }
            if ($this->getCurrentOrder()) {
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            }
            return $this;
        } else {
            return parent::setCollection($collection);
        }
    }
    
    /**
     * Get grit products sort order field
     *
     * @return string
     */
    public function getCurrentOrder()
    {
            $res = Mage::registry('ea_result');

        if ($res != null){
        	//get the order from easyask
        	$order = $this->_getData('_current_grid_order');
	        if ($order) {
	            return $order;
	        }
	        
	        $event_data_array  =  array(
	        		'relevance' => 'relevance',
    				'Product Name'  => 'name',
    				'Price' => 'price');
	        $varien_object = new Varien_Object($event_data_array);
	        Mage::dispatchEvent('catalog_controller_sortmap_update_get', array('varien_obj'=>$varien_object));
	        
	        $orders = $varien_object->getData();
	         	
	        $defaultOrder = $this->_orderField;
	
	        if (!isset($orders[$defaultOrder])) {
	            $keys = array_keys($orders);
	            $defaultOrder = $keys[0];
	        }
	
        	$order = substr($res->getSortOrder(), 0, strpos($res->getSortOrder(), ','));
        	if ($order === 'EAScore'){
        		$order = $defaultOrder;
        	}
	        if ($order && isset($orders[$order])) {
	            if ($order == $defaultOrder) {
	                Mage::getSingleton('catalog/session')->unsSortOrder();
	            } else {
	                $this->_memorizeParam('sort_order', $orders[$order]);
	            }
	        } else {
	            $order = Mage::getSingleton('catalog/session')->getSortOrder();
	        }
	        // validate session value
	        if (!$order || !isset($orders[$order])) {
	            $order = $defaultOrder;
	        }
	        $this->setData('_current_grid_order', $orders[$order]);
	        return $order;
        } else {
            return parent::getCurrentOrder();
        }
    }
    
    
}
?>