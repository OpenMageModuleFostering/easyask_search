<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

 /**
 * EasyAsk search helper
 *
 * @category   EasyAsk
 * @package    EasyAsk_Search
 * @author     EasyAsk
 */

class EasyAsk_Search_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function loadProductsFromMagentoDB() {
        return Mage::getStoreConfig('catalog/frontend/use_magento_db');
    }
    
    public function cacheAttributes($cacheKey, $res, $cache){
    	
    	$attributedata = array();
    	// As this is the first time a search is done, store it in cache
    	$attributes = $res->getAttributeNamesFull();
    	foreach ($attributes as $attribute) {
    		$attributeValues = null;
    		if ($res->getInitialDispLimitForAttrValues($attribute) > 0){
    			$attributeValues = $res->getDetailedAttributeValues($attribute, 1);
    			foreach ($res->getDetailedAttributeValuesFull($attribute) as $attrValue){
    				$attributeValues [] = $attrValue;
    			}
    		} else {
    			$attributeValues = $res->getDetailedAttributeValuesFull($attribute);
    		}
    		$attrvalues = array();
    		foreach ($attributeValues as  $attributeValue) {
    			$attrvalues[] = array('ns'=>$attributeValue->getNodeString(), 'ds'=>$attributeValue->getDisplayName(), 'pc'=>$attributeValue->getProductCount(), 'id'=>$res->getInitialDispLimitForAttrValues($attribute));
    		}
    		$attributedata [$attribute] = $attrvalues;
    	}
    	
    	// Save the initial attributes list if there is one
    	$isDispLimited = $res->isInitialDispLimitedForAttrNames();
    	if ($isDispLimited){
    		// If the initial display is limited then expand the number of attributes specified
    		$initialdisparray = $res->getInitialDisplayList(1);
    		$cache->save(serialize($initialdisparray), $cacheKey . '_initial', array("easyask_cache"), 60*60*24);
    	}
    	//                if (!$cache->load($queryText)){
    	$cache->save(serialize($attributedata), $cacheKey, array("easyask_cache"), 60*60*24);
    	$cache->save(serialize($attributedata), $cacheKey . '_original', array("easyask_cache"), 60*60*24);
    	//                }
    }

    public function updateCachedAttributes($queryText, $attributes, $res, $cache){
        //Now let us save the attribute values of all the attribute other than the selected attribute
        $allAttributes = unserialize(Mage::app()->getCache()->load($queryText));
        foreach ($attributes as $attribute){
            $path = $res->getCatPath();

            if(strpos($path, $attribute) == false){
                if ($res->getInitialDispLimitForAttrValues($attribute) > 0){
                    $attributeValues = $res->getDetailedAttributeValues($attribute, 1);
                    foreach ($res->getDetailedAttributeValuesFull($attribute) as $attrValue){
                        $attributeValues [] = $attrValue;
                    }
                } else {
                    $attributeValues = $res->getDetailedAttributeValuesFull($attribute);
                }
                $attrvalues = array();
                foreach ($attributeValues as  $attributeValue) {
                    $attrvalues[] = array('ns'=>$attributeValue->getNodeString(), 'ds'=>$attributeValue->getDisplayName(), 'pc'=>$attributeValue->getProductCount(), 'id'=>$res->getInitialDispLimitForAttrValues($attribute));
                }
                $allAttributes[$attribute] = $attrvalues;
            } else if ($this->isOnlyAttribute($res)){
                $origAttributes = unserialize(Mage::app()->getCache()->load($res->getOriginalQuestionAsked() . '_original'));
                if (in_array($attribute, array_keys($allAttributes))){
                    if (in_array($attribute, array_keys($origAttributes))){
                        $allAttributes[$attribute] = $origAttributes[$attribute];
                    }
                }
            }
        }

        $cache->save(serialize($allAttributes), $queryText, array("easyask_cache"), 60*60*24);
    }

    public function getCatPath($res){
    	$searchPath = $res->getBreadCrumbTrail()->getSearchPath();
//    	var_dump($searchPath); exit;
    	$eapathseo = $searchPath[count($searchPath) - 1]->getSEOPath();
    	 
    	$eapathseoarr = explode('/', $eapathseo);
    	$catPath = '';
    	foreach ($eapathseoarr as $eapathseo1) {
    		if (!(strpos($eapathseo1, '-', 0) === 0) && !(strpos($eapathseo1, ':') > 0)){
    			$catPath = strlen($catPath) > 0 ? $catPath . '-'. $eapathseo1 : $eapathseo1;
    		}
    	}
		return $catPath;
    }
    
    private function isOnlyAttribute($res){
    	$numAttrs = 0;
    	$numCategories = 0;
    	$searchPath = $res->getBreadCrumbTrail()->getSearchPath();
    	foreach ($searchPath as $path){
    		if ($path->getType() == 2){
    			$numAttrs += 1;
    		}
    		if($path->getType() == 1){
    			$numCategories +=1;
    		}
    	}
    	if (($numAttrs == 1)  && !($numCategories > 1)){
    		return true;
    	}
    	return false;
    }
}
