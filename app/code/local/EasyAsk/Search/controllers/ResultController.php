<?php

require_once('Mage/CatalogSearch/controllers/ResultController.php');

class EasyAsk_Search_ResultController extends Mage_CatalogSearch_ResultController
{
    protected $_helper = null;

    public function getHelper() {
        if ($this->_helper === null) {
            $this->_helper = Mage::helper('easyask_search');
        }
        return $this->_helper;
    }

    public function indexAction()
    {
        $queryText = Mage::helper('catalogsearch')->getQueryText();
        $bc    = $this->getRequest()->getParam('ea_bc', '');
        $eapath = $this->getRequest()->getParam('ea_path', '');
        $eacat   = $this->getRequest()->getParam('ea_c', '');
        
        $groupId = "";
        $login = Mage::getSingleton( 'customer/session' )->isLoggedIn(); //Check if User is Logged In
        if($login){
            $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId(); //Get Customers Group ID
        }

        $cache = Mage::app()->getCache();

        $query = Mage::helper('catalogsearch')->getQuery();
        /* @var $query Mage_CatalogSearch_Model_Query */

        $searchEngine = Mage::getStoreConfig('catalog/search/engine');

        $query->setStoreId(Mage::app()->getStore()->getId());

        if (strcasecmp($searchEngine, 'easyask_search/engine') === 0 ){//&& ($queryText != '' || $bc != '' || $eapath != '' || $eacat != '')) {

            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                ->setIsActive(1)
                ->setIsProcessed(1);
            }
            else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                }
                else {
                    $query->setPopularity(1);
                }

                if ($query->getRedirect()){
                    $query->save();
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                }
                else {
                    $query->prepare();
                }
            }

            $mode = $this->getRequest()->getParam('mode', 'grid');
            $limit = $this->getRequest()->getParam('limit', 0);
            $page  = $this->getRequest()->getParam('p');
            $order = $this->getRequest()->getParam('order', 'relevance');
            $dir   = $this->getRequest()->getParam('dir', 'asc');
            $attribSel   = $this->getRequest()->getParam('ea_a', '');

            $event_data_array  =  array(
            		'relevance' => 'relevance',               
           			'name'  => 'Product Name',
                	'price' => 'Price'
            );
            $varien_object = new Varien_Object($event_data_array);
            Mage::dispatchEvent('catalog_controller_sortmap_update_set', array('varien_obj'=>$varien_object));
            
            $attributeToColumn = $varien_object->getData();

            if ($limit !== 'all' && $limit == 0){
            	$limit = Mage::getSingleton('catalog/session')->getLimitPage();
            }
            
            $rpp = 0;
            if ($mode == 'grid'){
            	$rpp = ($limit === 0) ? Mage::getStoreConfig('catalog/frontend/grid_per_page') : $limit;
            } else {
                $rpp = ($limit === 0) ? Mage::getStoreConfig('catalog/frontend/list_per_page') : $limit;
            }

            Varien_Profiler::start('EASYASK::searchrequest1');

            // The call to create a RemoteEasyAsk object that allows you to access the remote server
            // to get the resultset of the search.  We need to to provide the server and port information
            //$ea = EasyAsk_Impl_RemoteFactory::create("75.150.81.116", 9120, "magento16");

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
            }else if (strlen($attribSel) > 0){
                $attrtype = substr($attribSel, 0, strpos($attribSel, ':'));
                $eapatharr = explode('/', $eapath);

                for($i = 0; sizeof($eapatharr) > $i; $i++){
                    if (!(strpos($eapatharr[$i], $attrtype . ':') === false)){
                        $attribSel = $eapatharr[$i] . ';' . $attribSel;
                        unset($eapatharr[$i]);
                    }
                    $eapath = implode('/', $eapatharr);
                }

                $res = $ea->userAttributeClick($eapath, $attribSel);

                $catPath = $this->getHelper()->getCatPath($res);
                if (strlen($catPath) > 0){
                	$this->getHelper()->updateCachedAttributes($res->getOriginalQuestionAsked(). '-'.$catPath,$res->getAttributeNamesFull(), $res, $cache);
                	$this->getHelper()->updateCachedAttributes($res->getOriginalQuestionAsked(). '-'.$catPath,$res->getCommonAttributeNames(true), $res, $cache);
                }else{
	                $this->getHelper()->updateCachedAttributes($queryText,$res->getAttributeNamesFull(), $res, $cache);
	                $this->getHelper()->updateCachedAttributes($queryText,$res->getCommonAttributeNames(true), $res, $cache);
                }
            }else if (strlen($eacat) > 0){
            	$res = $ea->userCategoryClick($eapath, $eacat);
            	$catPath = $this->getHelper()->getCatPath($res);
            	$this->getHelper()->updateCachedAttributes($res->getOriginalQuestionAsked(). '-'.$catPath,$res->getAttributeNamesFull(), $res, $cache);
                $this->getHelper()->updateCachedAttributes($res->getOriginalQuestionAsked(). '-'.$catPath,$res->getCommonAttributeNames(true), $res, $cache);
            }else if (strlen($bc) > 0){
                $res = $ea->userBreadCrumbClick($bc);

                $catPath = $this->getHelper()->getCatPath($res);
				if (strlen($catPath) > 0){
                	$this->getHelper()->updateCachedAttributes($queryText. '-'.$catPath,$res->getAttributeNamesFull(), $res, $cache);
                	$this->getHelper()->updateCachedAttributes($queryText. '-'.$catPath,$res->getCommonAttributeNames(true), $res, $cache);
                }else{
	                $this->getHelper()->updateCachedAttributes($queryText,$res->getAttributeNamesFull(), $res, $cache);
	                $this->getHelper()->updateCachedAttributes($queryText,$res->getCommonAttributeNames(true), $res, $cache);
                }
            }else{
                $res = $ea->userSearch($eapath, $queryText);
				$this->getHelper()->cacheAttributes($res->getOriginalQuestion(), $res, $cache);
            }


            Varien_Profiler::stop('EASYASK::searchrequest1');

            if ($res->isRedirect()) {
                $this->getResponse()->setRedirect($res->getRedirect());
                return;
            }

            if($res->getReturnCode() == -1  || $res->getTotalItems() == -1){
            	Mage::helper('catalogsearch')->setNoteMessages(array($res->getErrorMsg()));
            	$this->getResponse()->setRedirect(Mage::getBaseUrl() . 'no_results');
            	return;
            }else{
                Varien_Profiler::start('EASYASK::searchrequest2');

                //Check to see if the response has only one item in which case we redirect to the product page
                if ((Mage::getStoreConfig('catalog/search/auto_redirect') == 1) && $res->getTotalItems() === 1){
                	$this->getResponse()->setRedirect(Mage::getBaseUrl() . $res->getCellData(0, $res->getColumnIndex('Url Path')));
                }
                
                $path = $res->getCatPath();

                $commentary = $res->getCommentary();
                $originalQuestion = $res->getOriginalQuestion();

                $searchPath = $res->getBreadCrumbTrail()->getSearchPath();

                if (($res->getReturnCode() == -1) && (count($searchPath) === 0)){
                    throw new Exception("The search yields no results.  Please try again later.");
                }

                //            $realSearchQuery = $originalQuestion != '' ? $originalQuestion : $searchPath[count($searchPath) - 1]->getValue();
                $realSearchQuery = isset($searchPath[1]) ? $searchPath[1]->getValue() : $originalQuestion;

                Mage::register('ea_result', $res);



                if (strpos($commentary, 'Ignored:') !== false || strpos($commentary, 'Corrected Word:') !== false) {
                    Mage::helper('catalogsearch')->setNoteMessages(array(
                    sprintf("We found no results for '%s' but you may find the following results relevant:", $originalQuestion)
                    ));
                }

                Varien_Profiler::stop('EASYASK::searchrequest2');

            }
            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');

            if (isset($realSearchQuery)){
	            $searchTitle = sprintf("Search results for '%s'", $realSearchQuery);
	            $this->getLayout()->getBlock('head')->setTitle($searchTitle);
	            $this->getLayout()->getBlock('search.result')->setHeaderText($searchTitle);
            }

            $this->renderLayout();

            if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->save();
            }
        } else {
            $this->_redirectReferer();
        }

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