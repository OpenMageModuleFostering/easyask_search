<?php

class EasyAsk_Search_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_helper = null;

    public function getHelper() {
        if ($this->_helper === null) {
            $this->_helper = Mage::helper('easyask_search');
        }
        return $this->_helper;
    }
    
    public function catalogControllerCategoryInitAfter (Varien_Event_Observer $observer)
    {
        $searchEngine = Mage::getStoreConfig('catalog/search/engine');
        $useEA = Mage::getStoreConfig('catalog/navigation/use_easyask');
        if (strcasecmp($searchEngine, 'easyask_search/engine') === 0 && $useEA) {

            $category = $observer->getCategory();

            if ($category instanceof Mage_Catalog_Model_Category) {
                $url = $category->getUrl();
            } else {
                $url = $this->_getCategoryInstance()
                    ->setData($category->getData())
                    ->getUrl();
            }

            $eacat = str_replace('/', '_', substr($url, strlen(Mage::getBaseUrl())));
            $eapath = '';
            $groupId = "";
            $login = Mage::getSingleton( 'customer/session' )->isLoggedIn(); //Check if User is Logged In
            if($login){
                $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId(); //Get Customers Group ID
            }

            $cache = Mage::app()->getCache();

            $mode = $this->getRequest($observer)->getParam('mode', 'grid');
            $limit = $this->getRequest($observer)->getParam('limit', 0);
            $order = $this->getRequest($observer)->getParam('order', 'relevance');
            $dir   = $this->getRequest($observer)->getParam('dir', 'asc');
            $page  = $this->getRequest($observer)->getParam('p');
            $attribSel = $this->getRequest($observer)->getParam('ea_a', '');
            $eapath = $this->getRequest($observer)->getParam('ea_path', '');

            $event_data_array  =  array();
            $varien_object = new Varien_Object($event_data_array);
            Mage::dispatchEvent('catalog_controller_sortmap_update_set', array('varien_obj'=>$varien_object));
            
            $attributeToColumn = $varien_object->getSortArray();
            
            $attributes = Mage::getModel('catalog/config')->getAttributesUsedForSortBy();
            foreach ($attributes as $attribute) {
                $attrCode = $attribute->getAttributeCode();
                $eaAttrCode = ucwords(preg_replace('/_/', ' ', $attribute->getAttributeCode()));
                if ($attrCode == 'name') {
                    $eaAttrCode = 'Product ' . $eaAttrCode;
                }
                $attributeToColumn[$attrCode] = $eaAttrCode;
            }

	        if ($limit !== 'all' && $limit == 0){
            	$limit = Mage::getSingleton('catalog/session')->getLimitPage();
            }
            
            $rpp = 0;
	        if ($mode == 'grid'){
            	$rpp = ($limit == 0) ? Mage::getStoreConfig('catalog/frontend/grid_per_page') : $limit;
            } else {
                $rpp = ($limit == 0) ? Mage::getStoreConfig('catalog/frontend/list_per_page') : $limit;
            }
            
            Varien_Profiler::start('EASYASK::categoryrequest1');

            $eahostname = Mage::getStoreConfig('catalog/search/ea_server_hostname');
            $eaport = Mage::getStoreConfig('catalog/search/ea_server_port');
            $eadictionary = Mage::getStoreConfig('catalog/search/ea_dictionary');

            if ($eahostname == '' || $eadictionary == ''){
                throw new Exception("To use easyask search engine you need to specify the host name, port and dictionary in catalog search configuration screen in admin console.");
            }

            $ea = EasyAsk_Impl_RemoteFactory::create($eahostname, $eaport, $eadictionary);

            $opts = $ea->getOptions();
            $opts->setResultsPerPage($rpp);
            $opts->setGrouping("");
//            $opts->setToplevelProducts(true);
            $opts->setNavigateHierarchy(false);
            $opts->setSubCategories(false);
            $opts->setGroupId($groupId);
            $opts->setCustomerId("");
            //$opts->setCallOutParam("&eap_PriceCode=1");

            // Set sort field and direction
            if ($order != 'relevance') {
                $opts->setSortOrder($attributeToColumn[$order] . ',' . ($dir == 'asc' ? 't' : 'f'));
            }

            if ($page){
                $res = $ea->userGoToPage($eapath, $page);
            }
            else if (strlen($attribSel) > 0){
                $attrtype = substr($attribSel, 0, strpos($attribSel, ':'));
                $eapatharr = explode('/', $eapath);

                for($i = 0; sizeof($eapatharr) > $i; $i++){
                    if (!(strpos($eapatharr[$i], $attrtype . ':') === false)){
                        $attribSel = $eapatharr[$i] . ';' . $attribSel;
                        unset($eapatharr[$i]);
                    }
                    $eapath = implode('/', $eapatharr);
                }

                $res = $ea->userAttributeClick(strlen($eapath) > 0 ? $eapath : '/' . $eacat, $attribSel);

                $catPath = $this->getCatPath($res);
                $queryText = $res->getOriginalQuestionAsked();
                
                $this->getHelper()->updateCachedAttributes($catPath,$res->getAttributeNamesFull(), $res, $cache);
                $this->getHelper()->updateCachedAttributes($catPath,$res->getCommonAttributeNames(true), $res, $cache);
            }
            else {
                $res = $ea->userCategoryClick($eapath, $eacat);
            
                $catPath = $this->getCatPath($res);
                
				$this->getHelper()->cacheAttributes($catPath, $res, $cache);
            }

            Varien_Profiler::stop('EASYASK::categoryrequest1');
            /*if ($res->getFirstItem() == -1){
                return parent::catalogControllerCategoryInitAfter($observer);
            }*/

            if($res->getReturnCode() == -1){
                Mage::helper('catalogsearch')->setNoteMessages(array($res->getErrorMsg()));
            }else{
                Varien_Profiler::start('EASYASK::categoryrequest2');

                // Let us first search for shoes.  All Products is the top level category.
                $path = $res->getCatPath();

                $commentary = $res->getCommentary();
                $originalQuestion = $res->getOriginalQuestion();

                $searchPath = $res->getBreadCrumbTrail()->getSearchPath();

                if (($res->getReturnCode() == -1) && (count($searchPath) === 0)){
                    throw new Exception("The easyask search engine is not configured properly. To use easyask search engine you need to specify the host name, port and dictionary in catalog search configuration screen in admin console.");
                }

                $realSearchQuery = isset($searchPath[1]) ? $searchPath[1]->getValue() : $originalQuestion;

                Mage::register('ea_result', $res);

                if (strpos($commentary, 'Ignored:') !== false || strpos($commentary, 'Corrected Word:') !== false) {
                    Mage::helper('catalogsearch')->setNoteMessages(array(
                        sprintf("We found no results for '%s' but you may find the following results relevant:", $originalQuestion)
                    ));
                }

                Varien_Profiler::stop('EASYASK::categoryrequest2');

            }

        }

    }

    public function getRequest($observer) {
        return $observer->getControllerAction()->getRequest();
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