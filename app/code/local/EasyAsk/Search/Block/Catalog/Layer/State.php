<?php

class EasyAsk_Search_Block_Catalog_Layer_State extends Mage_Catalog_Block_Layer_State
{

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        $res = Mage::registry('ea_result');

        if ($res != null){

        	$searchPath = $res->getBreadCrumbTrail()->getSearchPath();
        	$eapath = $searchPath[1]->getSEOPath();
        	if (strpos ( $eapath, '-' ) === 0) {
        		 
	        	$params = array();
	        	$params['ea_path'] = $eapath;
	        	$params['ea_c'] = '';
	        	$params['ea_a'] = '';
	        	$params['ea_bc'] = '';
	        	
	        	$urlParams = array();
	        	$urlParams['_current']  = true;
	        	$urlParams['_escape']   = true;
	        	$urlParams['_use_rewrite']   = true;
	        	$urlParams['_query']    = $params;
	        	return $this->getUrl('*/*/*', $urlParams);
        	
        	} else {
        		$index = count($searchPath) >= 2 ? 1 : 0;
        		$eapath = $searchPath[$index]->getSEOPath();
        		
        		return Mage::getBaseUrl() . str_replace('_', '/', $eapath);
        	}        	
            
        } else {
            return parent::getClearUrl();
        }
    }

}
?>