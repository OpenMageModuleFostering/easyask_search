<?php
class EasyAsk_Search_Model_Resource_CatalogSearch_Fulltext_Collection extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection{
    /**
     * Loading state flag
     *
     * @var bool
     */
    protected $_isCollectionLoaded;
    /**
     * Retireve count of collection loaded items
     *
     * @return int
     */

    function count(){
        return count($this->_items);
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_items);
    }

    public function getSize()
    {
        $res = Mage::registry('ea_result');
        if ($res != null){
            $size = $res->getResultCount();
            return $size != -1? $size : 0;
        }else{
            return parent::getSize();
        }
    }

    public function addCountToCategories($categoryCollection)
    {

        $res = Mage::registry('ea_result');
        if ($res != null){
            $cats = $res->getDetailedCategories(0);
            $eacats = array();
            foreach($cats as $cat){
                $ids = $cat->getIds();
                $eacats[$ids[0]] = (String)$cat->getProductCount();
            }

            foreach ($categoryCollection as $category) {
                $_count = 0;
                if (isset($eacats[$category->getId()])) {
                    $_count = $eacats[$category->getId()];
                }
                $category->setProductCount($_count);
            }

            return $this;

        } else {
            return parent::addCountToCategories($categoryCollection);
        }
    }

    public function setIsLoaded($flag = true)
    {
        $this->_setIsLoaded($flag);
        return $this->isLoaded();
    }

    protected function _afterLoad()
    {

        $res = Mage::registry('ea_result');
        if ($res != null){

        }else{
            return parent::_afterLoad();
        }
    }


    /**
     * Retrieve collection loading status
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->_isCollectionLoaded;
    }

    /**
     * Set collection loading status flag
     *
     * @param unknown_type $flag
     * @return unknown
     */
    protected function _setIsLoaded($flag = true)
    {
        $this->_isCollectionLoaded = $flag;
        return $this;
    }

}