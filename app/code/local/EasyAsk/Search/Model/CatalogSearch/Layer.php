<?php
class EasyAsk_Search_Model_CatalogSearch_Layer extends Mage_CatalogSearch_Model_Layer {

    public function getFilterableAttributes()
    {

        $res = Mage::registry('ea_result');
        if ($res != null){

            $searchPath = $res->getBreadCrumbTrail()->getSearchPath();
            $eapath = $searchPath[count($searchPath) - 1]->getPath();
            
           	$originalAttributes = unserialize(Mage::app()->getCache()->load($res->getOriginalQuestionAsked() . '_original'));
           	
           	if (!$originalAttributes){ // In rare cases we may get a search result without a question.  This may happen when the user 
           		//selects a category from SAYT.  In this case the original Attributes are from the cache for the category selected
            	$eapathseo = $searchPath[count($searchPath) - 1]->getSEOPath();
            	
            	$eapathseoarr = explode('/', $eapathseo);
            	
            	$catPath = '';
            	foreach ($eapathseoarr as $eapathseo1) {
            		if (!(strpos($eapathseo1, '-', 0) === 0) && !(strpos($eapathseo1, ':') > 0)){
            			$catPath = strlen($catPath) > 0 ? $catPath . '-'. $eapathseo1 : $eapathseo1;
            		}
            	}
            	
            	if (strlen($catPath) > 0){
            		$originalAttributes = unserialize(Mage::app()->getCache()->load($res->getOriginalQuestionAsked() . '-'.$catPath));
            	} else {
            		$originalAttributes = unserialize(Mage::app()->getCache()->load($res->getOriginalQuestionAsked()));
            	}
            }
            
            $collection = null;

            $currentAttribute = '';
            $eapatharr = explode('////', $eapath);
            if (strpos($eapatharr[count($eapatharr) - 1], 'AttribSelect') !== false){
                $currentAttributePath = substr($eapatharr[count($eapatharr) - 1], 13);
                $currentAttribute = substr($currentAttributePath, 0, strpos($currentAttributePath, '='));
            }
            
//            $currentAttribute = substr($eapatharr[count($eapatharr) - 1], 0, strpos($eapatharr[count($eapatharr) - 1], ':'));

            $originalAttribNames = array();
            if ($originalAttributes){
                $originalAttribNames = array_keys($originalAttributes);
            }
            
            if (Mage::getStoreConfig('catalog/layered_navigation/ea_multiselect') == 1){
            	$commonAttribs = $res->getCommonAttributeNames(true);
            	foreach ($commonAttribs as $commonAttrib){
            		if (($commonAttrib == $currentAttribute) ){
            			$collection[]= $commonAttrib;
            		}
            	}
            }
            
            $attribs = $res->getAttributeNamesFull();
            foreach ($attribs as $attrib){
//                if (in_array($attrib, $originalAttribNames)){
                    $collection[] = $attrib;
//                }
            }
            
            $isDispLimited = $res->isInitialDispLimitedForAttrNames();
            $attribCollection = array();
            if ($collection){
                if ($isDispLimited){
                    $initialdisparray = unserialize(Mage::app()->getCache()->load($res->getOriginalQuestionAsked() . '_initial'));
                    // If the initial display is limited then expand the number of attributes specified
                    if (!$initialdisparray){
                        $initialdisparray = $res->getInitialDisplayList(1);
                    }
                    foreach ($collection as $item){
                    	$initDispLimited = $res->getInitialDispLimitForAttrValues($item) > 0 ? $res->getInitialDispLimitForAttrValues($item) : ($originalAttributes[$item][0]['id'] > 0 ? $originalAttributes[$item][0]['id'] : 0);
                        if (in_array($item, $initialdisparray)){
                            $attrib = array(
                            'name' => $item,
                            'expand' => true,
                            'dispLimited' => $initDispLimited,
							'isRangeFilter' => $res->isRangeFilter($item) 
                            );
                        }else{
                            $attrib = array(
                            'name' => $item,
                            'expand' => false,
                            'dispLimited' => $initDispLimited,
							'isRangeFilter' => $res->isRangeFilter($item)
                            );
                        }
                        $attribCollection[] = $attrib;
                    }

                }else{
                    // If the initial display is not limited then by default expand the first 3 attributes
                    $i = 0;
                    
                    foreach ($collection as $item){
                        $initDispLimited = $res->getInitialDispLimitForAttrValues($item) > 0 ? $res->getInitialDispLimitForAttrValues($item) : (in_array($item, $originalAttribNames) ? $originalAttributes[$item][0]['id'] : 0);
                        if ($i < 3){
                            $attrib = array(
                            'name' => $item,
                            'expand' => true,
                            'dispLimited' => $initDispLimited,
							'isRangeFilter' => $res->isRangeFilter($item)
                            );
                        } else {
                            $attrib = array(
                            'name' => $item,
                            'expand' => false,
                            'dispLimited' => $initDispLimited,
							'isRangeFilter' => $res->isRangeFilter($item)
                            );

                        }
                        $i++;
                        $attribCollection[] = $attrib;
                    }
                }
            }
            return $attribCollection;
        }

        return parent::getFilterableAttributes();
    }

    public function getStateAttributes()
    {

        $res = Mage::registry('ea_result');
        if ($res != null){

            $searchPath = $res->getBreadCrumbTrail()->getSearchPath();
            $eapath = $searchPath[count($searchPath) - 1]->getPath();

            $eapatharr = explode('////', $eapath);
            $attrarray = array();
            foreach ($eapatharr as $attrpath){
                if (strpos($attrpath, 'AttribSelect') !== false){
                    $attribsel = substr($attrpath, 13);
                    $attrarray[] = $attribsel;//substr($attribsel, 0, strpos($attribsel, '='));
                }
            }
             
            return $attrarray;
        }

    }

    /**
     * Prepare product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Resource_Product_Collection $collection
     * @return Mage_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $res = Mage::registry('ea_result');
        if ($res != null){

        }else{
            return parent::prepareProductCollection($collection);
        }
    }

}
