<?php
class EasyAsk_Search_Block_Html_Pager extends Mage_Page_Block_Html_Pager{

    public function getPagerUrl($params=array())
    {
        $res = Mage::registry('ea_result');

        if ($res != null){
            $searchPath = $res->getBreadCrumbTrail()->getSearchPath();
            $eapath = $searchPath[count($searchPath) - 1]->getSEOPath();
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
            return parent::getPagerUrl($params);
        }
    }

}
?>