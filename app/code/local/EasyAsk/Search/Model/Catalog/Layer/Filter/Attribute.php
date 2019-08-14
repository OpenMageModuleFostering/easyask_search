<?php

class EasyAsk_Search_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
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
	                    isset($itemData['ea_bc'])?$itemData['ea_bc']:'',
	                    isset($itemData['isDisabled'])?$itemData['isDisabled']:'',
	                    isset($itemData['isChecked'])?$itemData['isChecked']:'',
	                    isset($itemData['minValue'])?$itemData['minValue']:'',
	                    isset($itemData['maxValue'])?$itemData['maxValue']:'',
						isset($itemData['minRangeValue'])?$itemData['minRangeValue']:'',
	                	isset($itemData['maxRangeValue'])?$itemData['maxRangeValue']:'',
	                	isset($itemData['rangeRound'])?$itemData['rangeRound']:'',
	                	isset($itemData['ea_seoAttr'])?$itemData['ea_seoAttr']:'',
	                	isset($itemData['imageURL'])?$itemData['imageURL']:''
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
    protected function _createItem($label, $value, $count=0, $eapath='', $eacat='', $eaattrib='', $eabc='', $isDisabled='', $isChecked=false, 
    		$minValue='', $maxValue='', $minRangeValue='', $maxRangeValue='', $rangeRound='', $eaSeoAttr='', $imageURL='')
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
	            ->setEabc($eabc)
	            ->setIsDisabled($isDisabled)
	            ->setIsChecked($isChecked)
	            ->setMinValue($minValue)
	            ->setMaxValue($maxValue)
	            ->setMinRangeValue($minRangeValue)
	            ->setMaxRangeValue($maxRangeValue)
	            ->setRangeRound($rangeRound)
	            ->setEaSeoAttr($eaSeoAttr)
	            ->setImageURL($imageURL);
    	} else {
    		return parent::_createItem($label, $value, $count);
    	}
    }

    /**
     * Set attribute model to filter
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    public function setAttributeModel($attribute)
    {
        $res = Mage::registry('ea_result');
        if ($res != null){
	    	$this->setRequestVar($attribute);
	        $this->setData('attribute_model', $attribute);
	        return $this;
        } else {
        	return parent::setAttributeModel($attribute);
        }
    }

    /**
     * Get filter text label
     *
     * @return string
     */
    public function getName()
    {
        $res = Mage::registry('ea_result');
        if ($res != null){
            return strpos($this->getAttributeModel(), '=') > 0 ? substr($this->getAttributeModel(), 0, strpos($this->getAttributeModel(), '=')) : $this->getAttributeModel();
        } else {
            return $this->getAttributeModel()->getStoreLabel();
        }
    }

    /**
     * Retrieve Attribute code
     *
     * @return int
     */
    public function getAttributeCode()
    {
        $attributeCode = $this->_getData('attribute_code');
        return $attributeCode;
    }


    /**
     * Set Attribute code
     *
     * @param int $attributeCode
     * @return Mage_Catalog_Model_Layer_Filter_Abstract
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->setData('attribute_code', $attributeCode);
    }

    /**
     * Get option text from frontend model by option id
     *
     * @param   int $optionId
     * @return  unknown
     */
    protected function _getOptionText($optionId)
    {
        $res = Mage::registry('ea_result');
        if ($res != null){
            return $this->getAttributeModel();
        } else {
            return parent::_getOptionText($optionId);
        }
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Varien_Object $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $res = Mage::registry('ea_result');
        if ($res != null){
            if ($this->getAttributeCode() == '1'){

                $text = $this->_getOptionText(null);

                if ($text) {
                    //            $this->_getResource()->applyFilterToCollection($this, $filter);

                    //Get the current path and exclude the selected attribute form the path
                    $removepath = '';
                    $searchPath = $res->getBreadCrumbTrail()->getSearchPath();
                    $eapathseo = $searchPath[count($searchPath) - 1]->getSEOPath();
                    $eavaluepath = $searchPath[count($searchPath) - 1]->getPath();

                    $eapathseoarr = explode('/', $eapathseo);
                    $eavaluepatharr = explode('////', $eavaluepath);
                    for($i = 0; sizeof($eapathseoarr) > $i; $i++){
                        if (strpos($eapathseoarr[$i], ':') > 0){
                            $eavalue = substr($eavaluepatharr[$i + 1], 13);
                            $eapath = $eapathseoarr[$i];
                            if (strcmp($eavalue, $text) == 0){
                                $eapatharrsize = sizeof(explode (';', $eapath));
                                if ($eapatharrsize > 1){ // multi select
                                    unset($eapathseoarr[$i]);
                                    $removepath = implode('/', $eapathseoarr);
                                    $filter = '';
                                    for ($j = 0; $eapatharrsize > $j; $j++){
                                        $eapatharr = explode (';;;;', $eavalue);
                                        $eaval = $eapatharr[$j];
                                        if (strpos($eaval, '=') !== false){
                                            $filterSub = substr($eaval, strpos($eaval, '=') + 1);
                                        }
                                        $filter = $filter . '<div>' . str_replace("'", "", $filterSub) . '</div>';
                                    }
                                    $this->getLayer()->getState()->addFilter($this->_createItem($filter, $filter, 0, '', '', '', $removepath));
                                }else{
                                    unset($eapathseoarr[$i]);
                                    $removepath = implode('/', $eapathseoarr);
//                                    if (strpos($text, '=') !== false){
//                                        $filter = substr($text, strpos($text, '=') + 1);
//                                    }
                                    $filter = $searchPath[$i + 1]->getValue();
                                    $this->getLayer()->getState()->addFilter($this->_createItem($filter, $filter, 0, '', '', '', $removepath));
                                }
                            }
                        }

                    }

                    $this->_items = array();
                }
            }
            return $this;
        } else {
            return parent::apply($request, $filterBlock);
        }

    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $res = Mage::registry('ea_result');

        if ($res != null){
            $attribute = $this->getAttributeModel();
            $this->_requestVar = $attribute;

            $key = $this->getLayer()->getStateKey().'_'.$this->_requestVar;
            $data = $this->getLayer()->getAggregator()->getCacheData($key);

            if ($data === null) {

                $searchPath = $res->getBreadCrumbTrail()->getSearchPath();
                $eapathseo = $searchPath[count($searchPath) - 1]->getSEOPath();

                $eapathseoarr = explode('/', $eapathseo);
                $currentAttribute = substr($eapathseoarr[count($eapathseoarr) - 1], 0, strpos($eapathseoarr[count($eapathseoarr) - 1], ':'));

                $catPath = $this->getCatPath($res);
                
                $query = $res->getOriginalQuestionAsked();
                $allAttributeValues = null;
                $allAttributes = unserialize(Mage::app()->getCache()->load($catPath));
                if ($allAttributes){
                    $allAttributeValues = $allAttributes[$attribute];
                }

                $attributeValues = $res->getDetailedAttributeValuesFull($attribute);
                $attributeCommonValues = $res->getDetailedCommonAttributeValuesFull($attribute);

                $attributeValues = array_merge($attributeValues, $attributeCommonValues);
                $attributeValuesKeys = array();
                foreach ($attributeValues as $attributeValue){
                    $attributeValuesKeys[] = $attributeValue->getNodeString();
                }

                if ($allAttributeValues){
                    foreach ($allAttributeValues as $allAttributeValue) {
                        $isSelectedValue = false;
                        if (in_array($allAttributeValue['ns'], $attributeValuesKeys)){
                            foreach ($attributeValues as $attributeValue){
                                if (($allAttributeValue['ds'] == $attributeValue->getDisplayName())){
                                    $eapathseoarrcopy = $eapathseoarr;
                                    for($i = 0; sizeof($eapathseoarrcopy) > $i; $i++){
                                        if (strpos($eapathseoarrcopy[$i], ':') > 0){

                                            $eapath = $eapathseoarrcopy[$i];
                                            $eapatharrsize = sizeof(explode (';', $eapath));
                                            if ($eapatharrsize > 1){ // multi select
                                                for ($j = 0; $eapatharrsize > $j; $j++){
                                                    $eapatharr = explode (';', $eapath);
                                                    $eaval = $eapatharr[$j];
                                                    if ($eaval == $attributeValue->getNodeString()){
                                                        $isSelectedValue = true;
                                                        unset($eapatharr[$j]);
                                                        $eapathseoarrcopy[$i] = implode(';', $eapatharr);
                                                        $removepath = implode('/', $eapathseoarrcopy);
                                                    }
                                                }
                                            }else{
                                                if ($eapathseoarrcopy[$i] == $attributeValue->getNodeString()){
                                                    $isSelectedValue = true;
                                                    unset($eapathseoarrcopy[$i]);
                                                    $removepath = implode('/', $eapathseoarrcopy);
                                                }
                                            }
                                        }
                                    }
                                    
                                    if (!(strpos($allAttributeValue['ns'], $currentAttribute . ':') === false)){
                                        $data[] = array(
	                                        'label' => Mage::helper('core')->htmlEscape($attributeValue->getDisplayName()),
	                            			'value' => $allAttributeValue['ns'],
	                            			'count' => $allAttributeValue['pc'],
	                                        'ea_path' => $eapathseo,
	            	    					'ea_a' => $allAttributeValue['ns'],
	                                        'ea_bc' => $isSelectedValue ? $removepath : '',
	                                        'isDisabled' => false,
	                                        'isChecked' => $isSelectedValue ? true : false,
	                                        'minValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMinValue() : '',
	                                        'maxValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMaxValue() : '',
	                                        'minRangeValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMinRangeValue() : '',
	                                        'maxRangeValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMaxRangeValue() : '',
	                                        'rangeRound' => $attributeValue->getValueType() == 2 ? $attributeValue->getRangeRound() : '',
	                                        'ea_seoAttr' => $attributeValue->getValueType() == 2 ? substr($attributeValue->getNodeString(), 0, strpos($attributeValue->getNodeString(), ':')) : ''//,
//	                                        'imageURL' => $this->getImageURL($attribute, $attributeValue->getDisplayName())
                                        );
                                    } else {
                                        $data[] = array(
	                                        'label' => Mage::helper('core')->htmlEscape($attributeValue->getDisplayName()),
	                                    	'value' => $attributeValue->getNodeString(),
	                                        'count' => $attributeValue->getProductCount(),
	                                       	'ea_path' => $eapathseo,
	            	    					'ea_a' => $attributeValue->getNodeString(),
	                                        'ea_bc' => $isSelectedValue ? $removepath : '',
	                                        'isDisabled' => false,
	                                        'isChecked' => $isSelectedValue ? true : false,
	                                        'minValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMinValue() : '',
	                                        'maxValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMaxValue() : '',
	                                        'minRangeValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMinRangeValue() : '',
	                                        'maxRangeValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMaxRangeValue() : '',
	                                        'rangeRound' => $attributeValue->getValueType() == 2 ? $attributeValue->getRangeRound() : '',
	                                        'ea_seoAttr' => $attributeValue->getValueType() == 2 ? substr($attributeValue->getNodeString(), 0, strpos($attributeValue->getNodeString(), ':')) : ''//,
//	                                        'imageURL' => $this->getImageURL($attribute, $attributeValue->getDisplayName())
                                        );
                                    }
                                }
                            }
                        } else {
                            if (!(strpos($allAttributeValue['ns'], $currentAttribute . ':') === false)){
                                $data[] = array(
                            		'label' => $attributeValue->getValueType() == 2 ? Mage::helper('core')->htmlEscape($attributeValue->getDisplayName()) : Mage::helper('core')->htmlEscape($allAttributeValue['ds']),
                            		'value' => $allAttributeValue['ns'],
                            		'count' => $allAttributeValue['pc'],
                        			'ea_path' => $eapathseo,
    								'ea_a' => $allAttributeValue['ns'],
                            		'isDisabled' => false,
                            		'isChecked' => false,
									'minValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMinValue() : '',
                            		'maxValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMaxValue() : '',
                            		'minRangeValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMinRangeValue() : '',
                            		'maxRangeValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMaxRangeValue() : '',
                            		'rangeRound' => $attributeValue->getValueType() == 2 ? $attributeValue->getRangeRound() : '',
	                                'ea_seoAttr' => $attributeValue->getValueType() == 2 ? substr($attributeValue->getNodeString(), 0, strpos($attributeValue->getNodeString(), ':')) : ''//,
//                            		'imageURL' => $this->getImageURL($attribute, $allAttributeValue['ds'])
                                );
                            }
                            else {
                                $data[] = array(
                            		'label' => $attributeValue->getValueType() == 2 ? Mage::helper('core')->htmlEscape($attributeValue->getDisplayName()) : Mage::helper('core')->htmlEscape($allAttributeValue['ds']),
                                    'value' => $allAttributeValue['ns'],
                                    'count' => $allAttributeValue['pc'],
                                    'ea_path' => $eapathseo,
                                    'ea_a' => $allAttributeValue['ns'],
                                    'isDisabled' => true,
                                    'isChecked' => false,
                                    'minValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMinValue() : '',
                                    'maxValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMaxValue() : '',
                                    'minRangeValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMinRangeValue() : '',
                                    'maxRangeValue' => $attributeValue->getValueType() == 2 ? $attributeValue->getMaxRangeValue() : '',
                                    'rangeRound' => $attributeValue->getValueType() == 2 ? $attributeValue->getRangeRound() : '',
	                                'ea_seoAttr' => $attributeValue->getValueType() == 2 ? substr($attributeValue->getNodeString(), 0, strpos($attributeValue->getNodeString(), ':')) : ''//,
//                                  'imageURL' => $this->getImageURL($attribute, $allAttributeValue['ds'])
                                );
                            }
                        }
                    }
                }

                $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG.':'.$attribute
                );

                $tags = $this->getLayer()->getStateTags($tags);
                $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
            }
            return $data;
        } else {
            return parent::_getItemsData();
        }

    }

    private function getImageURL($attType, $attVal){
        $imageURL = Mage::getBaseDir('media'). '/catalog/images/' . urlencode(strtolower($attType)) . '/' . urlencode(strtolower($attVal)) . '.jpg';
        if ((bool)@getimagesize($imageURL) === true){
            return $imageURL;
        } else {
            return "";
        }
    }
    
    private function getCatPath($res){
    	$searchPath = $res->getBreadCrumbTrail()->getSearchPath();
    	$eapath = $searchPath[count($searchPath) - 1]->getPath();
    	
    	$eapatharr = explode('////', $eapath);
    	$catPath = '';
    	foreach ($eapatharr as $eapath1) {
    		if (!(strpos($eapath1, 'AttribSelect', 0) === 0) && !(strpos($eapath1, '=') > 0)){
    			$catPath = strlen($catPath) > 0 ? $catPath . '-'. $eapath1 : $eapath1;
    		}
    	}
    	return $catPath;
    }
        
}