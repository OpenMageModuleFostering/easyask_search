<?php
class EasyAsk_Search_Block_CatalogSearch_Result extends Mage_CatalogSearch_Block_Result {

    /**
     * Prepare layout
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    protected function _prepareLayout()
    {
        $res = Mage::registry('ea_result');
        if ($res != null){
            // add Home breadcrumb
            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbs) {
                $searchPath = $res->getBreadCrumbTrail()->getSearchPath();

                $breadcrumbs->addCrumb('home', array(
                'label' => $this->__('Home'),
                'title' => $this->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
                ));
                if (count($searchPath) > 0){
                    for($i = 1; $i < count($searchPath) ; $i++){
                        if ($i != count($searchPath) - 1){
			                $seopath = $searchPath[$i]->getSEOPath();
			                $link = '?q='.Mage::helper('catalogsearch')->getQueryText().'&ea_bc='. $seopath;
                            if ($searchPath[$i]->getType() == 1){
                                $link = $link . '&cat=' . $searchPath[$i]->getValue();
                            } else if ($searchPath[$i]->getType() == 2){
                                $eapath = $searchPath[$i]->getPath();
                                $eapatharr = explode('////', $eapath);
			                    $attrarray = array();
			                    foreach ($eapatharr as $attrpath){
				                    if (strpos($attrpath, 'AttribSelect') !== false){
				                        $attribsel = substr($attrpath, 13);
					                    $attrarray[] = substr($attribsel, 0, strpos($attribsel, '='));
				                    }
			                    }
                                $seopatharr = explode('/', $seopath);
			                    $attrvalarray = array();
			                    foreach ($seopatharr as $seopathpart){
				                    if (strpos($seopathpart, ':') !== false){
					                    $attrvalarray[] = $seopathpart;
				                    }
			                    }
			                    
			                    if (sizeof($attrarray) == sizeof($attrvalarray)){
			                        for($j = 0; $j < sizeof($attrarray); $j++){
			                            $link = $link . '&' . $attrarray[$j] . '=' . $attrvalarray[$j];
			                        }
			                    }
			                    
                            }			                    
                            $breadcrumbs->addCrumb($searchPath[$i]->getValue(), array(
 			                    'label' => $searchPath[$i]->getValue(),
                			    'title' => $searchPath[$i]->getValue(),
			        			'link'	=> $link
                            ));
                        } else {
                            $breadcrumbs->addCrumb($searchPath[$i]->getValue(), array(
			                'label' => $searchPath[$i]->getValue(),
            			    'title' => $searchPath[$i]->getValue()
                            ));
                        }
                    }
                }
            }

            // modify page title
            $title = $this->__("Search results for: '%s'", $this->helper('catalogsearch')->getEscapedQueryText());
            $this->getLayout()->getBlock('head')->setTitle($title);

            return;
        }

        return parent::_prepareLayout();
    }
    
    public function hasBanner($type){
		$hasBanner = false;
		$res = Mage::registry('ea_result');
		if ($res != null){
			$hasBanner = $res->hasBanner($type);
		}
		return $hasBanner;    	
    }
    
    public function getBanner($type){
    	$res = Mage::registry('ea_result');
    	if ($res != null){
    		$banner = $res->getBanner($type);
    		$banneridentifier = $banner->getBlockIdentifier();
    		
    		$html = '';
   			$block = Mage::getModel('cms/block')
	   			->setStoreId(Mage::app()->getStore()->getId())
    			->load($banneridentifier);
    		if ($block->getIsActive()) {
    			/* @var $helper Mage_Cms_Helper_Data */
    			$helper = Mage::helper('cms');
    			$processor = $helper->getBlockTemplateProcessor();
    			$html = $processor->filter($block->getContent());
    			$this->addModelTags($block);
    		}
    		return $html;
    		
    	}
    	
    }

}
?>