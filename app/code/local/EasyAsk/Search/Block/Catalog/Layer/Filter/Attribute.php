<?php
class EasyAsk_Search_Block_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    protected function _prepareFilter()
    {
        $res = Mage::registry('ea_result');
        if ($res != null){
	    	$this->_filter->setAttributeModel($this->getAttributeModel());
	        $this->_filter->setAttributeCode($this->getAttributeCode());
	        $this->_filter->setIsAttributeSelected($this->_isAttributeSelected());
	        return $this;
        } else {
        	return parent::_prepareFilter();
        }
    }
    
    protected function _isAttributeSelected(){
        $isCurrentAttribute = false;
        $res = Mage::registry('ea_result');
        if ($res != null){
            $searchPath = $res->getBreadCrumbTrail()->getSearchPath();
            if (count($searchPath) > 0){
                for($i = 1; $i < count($searchPath) ; $i++){
                    $eapath = $searchPath[$i]->getPath();
                    $eapatharr = explode('////', $eapath);
                    $attrarray = array();
                    foreach ($eapatharr as $attrpath){
                        if (strpos($attrpath, 'AttribSelect') !== false){
                            $attribsel = substr($attrpath, 13);
    	                    $attrib = substr($attribsel, 0, strpos($attribsel, '='));
                            if ($attrib === $this->getAttributeModel()){
                                $isCurrentAttribute = true;
                            }
                        }
                    }
                }
            }
        }
        return $isCurrentAttribute;
    }
    
}
?>