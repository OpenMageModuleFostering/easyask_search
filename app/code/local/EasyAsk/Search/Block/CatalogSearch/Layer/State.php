<?php

class EasyAsk_Search_Block_CatalogSearch_Layer_State extends Mage_Catalog_Block_Layer_State
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
            return parent::getClearUrl();
        }
    }

}
?>