<?php
class EasyAsk_Search_Block_CatalogSearch_Layer extends Mage_CatalogSearch_Block_Layer {


    /**
     * Get all layer filters
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = array();
        if ($categoryFilter = $this->_getCategoryFilter()) {
            $filters[] = $categoryFilter;
        }

        $filterableAttributes = $this->_getFilterableAttributes();
        foreach ($filterableAttributes as $attribute) {
            $filters[] = $this->getChild($attribute['name'] . '_filter');
        }

        return $filters;
    }



    /**
     * Get all fiterable attributes of current category
     *
     * @return array
     */
    protected function _getFilterableAttributes()
    {
        $attributes = $this->getData('_filterable_attributes');
        if (is_null($attributes)) {
            $attributes = $this->getLayer()->getFilterableAttributes();
            $this->setData('_filterable_attributes', $attributes);
        }

        return $attributes;
    }

    /**
     * Get all fiterable attributes of current category
     *
     * @return array
     */
    protected function _getStateAttributes()
    {
        $attributes = $this->getData('_state_attributes');
        if (is_null($attributes)) {
            $attributes = $this->getLayer()->getStateAttributes();
            $this->setData('_state_attributes', $attributes);
        }

        return $attributes;
    }

    /**
     * Prepare child blocks
     *
     * @return Mage_Catalog_Block_Layer_View
     */
    protected function _prepareLayout()
    {
    	
    	$res = Mage::registry('ea_result');
    	$categoryCount = 0;
    	if ($res != null){
    		$categoryCount = $res->getInitDisplayLimitForCategories();
//    	}
    	
    	
        $stateBlock = $this->getLayout()->createBlock($this->_stateBlockName)
        ->setLayer($this->getLayer());

        if ($categoryCount > 0){
        	$categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
        	->setLayer($this->getLayer())
            ->setIsInitDisplayLimited($categoryCount)
            ->init();
        } else {
        	$categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
        	->setLayer($this->getLayer())
        	->init();
        }

        $this->setChild('layer_state', $stateBlock);
        $this->setChild('category_filter', $categoryBlock);

        $filterableAttributes = $this->_getFilterableAttributes();
        $stateAttributes = $this->_getStateAttributes();
        $selectedAttributes = array();
        
        foreach ($stateAttributes as $selectedAttribute){
            $selectedAttributes[] = substr($selectedAttribute, 0, strpos($selectedAttribute, '='));    
        }
        
        foreach ($filterableAttributes as $attribute) {
            $filterBlockName = $this->_attributeFilterBlockName;
            if ($attribute['expand'] == true || in_array($attribute['name'], $selectedAttributes)){
                $this->setChild($attribute['name'] . '_filter',
                $this->getLayout()->createBlock($filterBlockName)
                ->setLayer($this->getLayer())
                ->setAttributeModel($attribute['name'])
                ->setAttributeCode('2')
                ->setIsInitDisplayLimited($attribute['dispLimited'])
                ->setIsRangeFilter($attribute['isRangeFilter'])
				->init());
            } else {
                if ($attribute['name']){
                    $this->setChild($attribute['name'] . '_filter',
                    $this->getLayout()->createBlock($filterBlockName)
                    ->setLayer($this->getLayer())
                    ->setAttributeModel($attribute['name'])
                    ->setAttributeCode('0')
                    ->setIsInitDisplayLimited($attribute['dispLimited'])
                	->setIsRangeFilter($attribute['isRangeFilter'])
					->init());
                }
            }
        }

        foreach ($stateAttributes as $attribute) {
            $filterBlockName = $this->_attributeFilterBlockName;
            $this->setChild($attribute . '_state_filter',
            $this->getLayout()->createBlock($filterBlockName)
            ->setLayer($this->getLayer())
            ->setAttributeModel($attribute)
            ->setAttributeCode('1')
			->init());
        }

        $this->getLayer()->apply();
        return;

    } else {
    	return parent::_prepareLayout();
    }
}
}