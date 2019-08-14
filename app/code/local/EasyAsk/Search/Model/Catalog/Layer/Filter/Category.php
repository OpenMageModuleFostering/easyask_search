<?php

class EasyAsk_Search_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category
{
	
 	/**
     * Initialize filter items
     *
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    protected function _initItems()
    {
        $res = Mage::registry('ea_result');
        if ($res != null){
	    	$data = $this->_getItemsData();
	        $items=array();
	        if ($data != null){
	            foreach ($data as $itemData) {
	            	$items[] = $this->_createItem(
	                $itemData['label'],
	                $itemData['value'],
	                $itemData['count'],
	                isset($itemData['ea_path'])?$itemData['ea_path']:'',
	                isset($itemData['ea_c'])?$itemData['ea_c']:'',
	                isset($itemData['ea_a'])?$itemData['ea_a']:'',
	                isset($itemData['ea_bc'])?$itemData['ea_bc']:''
	                );
	            }
	        }
	        $this->_items = $items;
	        return $this;
        } else {
        	return parent::_initItems();
        }
    }
    
    
    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count=0, $eapath='', $eacat='', $eaattrib='', $eabc='')
    {
    	$res = Mage::registry('ea_result');
    	if ($res != null){
	    	return Mage::getModel('catalog/layer_filter_item')
	    	->setFilter($this)
	    	->setLabel($label)
	    	->setValue($value)
	    	->setCount($count)
	    	->setEapath($eapath)
	    	->setEacat($eacat)
	    	->setEaattrib($eaattrib)
	    	->setEabc($eabc);
    	} else {
    		return parent::_createItem($label, $value, $count);
    	}
    }

    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {

        $res = Mage::registry('ea_result');

        if ($res != null){
            $searchPath = $res->getBreadCrumbTrail()->getSearchPath();
            $eapath = $searchPath[count($searchPath) - 1]->getSEOPath();
            $eavaluepath = $searchPath[count($searchPath) - 1]->getPath();

            $eapatharr = explode('/', $eapath);
            $eavaluepatharr = explode('////', $eavaluepath);

            for($i = 0; sizeof($eapatharr) > $i; $i++){
                if (!(strpos($eapatharr[$i], '-') === 0 || strpos($eapatharr[$i], ':') > 0)){
                    if ($eapatharr[$i]){
                        $copyarray = $this->arrayCopy($eapatharr);
                        $filter = $eavaluepatharr[$i + 1];
                        unset($copyarray[$i]);
                        for ($j = $i + 1; sizeof($copyarray) >= $j; $j++){
			                if (!(strpos($copyarray[$j], '-') === 0 || strpos($copyarray[$j], ':') > 0)){
			                	unset($copyarray[$j]);
			                }
                        }
                        $removepath = implode('/', $copyarray);
                        $this->getLayer()->getState()->addFilter(
                            $this->_createItem($filter, $filter, 0, '','','', $removepath));
                    }
                }
            }

            return $this;
        }else{
            return parent::apply($request, $filterBlock);
        }

    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $res = Mage::registry('ea_result');
        if ($res != null){
            $key = $this->getLayer()->getStateKey().'_SUBCATEGORIES';
            $data = $this->getLayer()->getAggregator()->getCacheData($key);

            if ($data === null) {
                $res = Mage::registry('ea_result');

                $searchPath = $res->getBreadCrumbTrail()->getSearchPath();
                $eapath = $searchPath[count($searchPath) - 1]->getSEOPath();

                $categories = array();
                if ($res->getInitDisplayLimitForCategories() > 0){
                    $categories = $res->getDetailedCategories(1);
                    foreach ($res->getDetailedCategoriesFull() as $cat){
                        $categories [] = $cat;
                    }
                } else {
                    $categories = $res->getDetailedCategoriesFull();
                }

                foreach ($categories as $category) {
//                    if ($category->getProductCount()) {
                    $catids = $category->getIDs();
                    $data[] = array(
                        'label' => Mage::helper('core')->htmlEscape($category->getName()),
                        'value' => $catids[0],
                        'count' => $category->getProductCount(),
                        'ea_c' => $category->getNodeString(),
                        'ea_path' => $eapath
                    );
//                    }
                }

                $tags = $this->getLayer()->getStateTags();
                $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
            }
            return $data;
        } else {
            return parent::_getItemsData();
        }
    }

    private function arrayCopy( array $array ) {
        $result = array();
        foreach( $array as $key => $val ) {
            if( is_array( $val ) ) {
                $result[$key] = arrayCopy( $val );
            } elseif ( is_object( $val ) ) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
    }
   
}
