<?php
class EasyAsk_Search_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item {
	
	/**
	 * Initialize filter items
	 *
	 * @return Mage_Catalog_Model_Layer_Filter_Abstract
	 */
	protected function _initItems() {
        $res = Mage::registry('ea_result');
        if ($res != null){
			$data = $this->_getItemsData ();
			$items = array ();
			if ($data != null) {
				foreach ( $data as $itemData ) {
					$items [] = $this->_createItem ( $itemData ['label'], $itemData ['value'], $itemData ['count'], isset ( $itemData ['ea_path'] ) ? $itemData ['ea_path'] : '', isset ( $itemData ['ea_c'] ) ? $itemData ['ea_c'] : '', isset ( $itemData ['ea_a'] ) ? $itemData ['ea_a'] : '', isset ( $itemData ['ea_bc'] ) ? $itemData ['ea_bc'] : '' );
				}
			}
			$this->_items = $items;
			return $this;
        } else {
        	return parent::_initItems();
        }
	}
	
	/**
	 * Create filter item object
	 *
	 * @param string $label        	
	 * @param mixed $value        	
	 * @param int $count        	
	 * @return Mage_Catalog_Model_Layer_Filter_Item
	 */
	protected function _createItem($label, $value, $count = 0, $eapath = '', $eacat = '', $eaattrib = '', $eabc = '') {
        $res = Mage::registry('ea_result');
        if ($res != null){
			return Mage::getModel ( 'catalog/layer_filter_item' )->setFilter ( $this )->setLabel ( $label )->setValue ( $value )->setCount ( $count )->setEapath ( $eapath )->setEacat ( $eacat )->setEaattrib ( $eaattrib )->setEabc ( $eabc );
        } else {
        	return parent::_createItem($label, $value, $count);
        }
	}
	
	/**
	 * Get filter item url
	 *
	 * @return string
	 */
	public function getUrl() {
		$res = Mage::registry ( 'ea_result' );
		
		if ($res != null) {
			if (strlen ( $this->getEacat () ) > 0 && !(strpos ( $this->getEapath() , '-' ) === 0)) {
				// Category handling
				$catPath = $this->getEacat ();
				// check for any attributes selected
				$pathArr = explode ( '/', $this->getEapath () );
				$attrArray = array ();
				for($i = 0; $i < sizeof ( $pathArr ); $i ++) {
					if (strpos ( $pathArr [$i], ":" ) > 0) {
						$attrArray [] = $pathArr [$i];
					}
				}
				$param = '';
				if (sizeof ( $attrArray ) > 0) {
					for($j = 0; $j < sizeof ( $attrArray ); $j ++) {
						if ($j == 0) {
							$param = $attrArray [$j];
						} else {
							$param = $param . '&' . $attrArray [$j];
						}
					}
				}
				return Mage::getBaseUrl () . str_replace ( '_', '/', $catPath ) . (strlen ( $param ) === 0 ? '' : '?ea_a=' . $param);
			} else {
				$query = array (
						$this->getFilter ()->getRequestVar () => $this->getValue (),
						Mage::getBlockSingleton ( 'page/html_pager' )->getPageVarName () => null, // exclude current page from urls
						'ea_path' => $this->getEapath (),
						'ea_c' => $this->getEacat (),
						'ea_a' => $this->getEaattrib (),
						'ea_bc' => '' 
				);
				return Mage::getUrl ( '*/*/*', array (
						'_current' => true,
						'_use_rewrite' => true,
						'_query' => $query 
				) );
			}
		} else {
			return parent::getUrl ();
		}
	}
	
	/**
	 * Get url for remove item from filter
	 *
	 * @return string
	 */
	public function getRemoveUrl() {
		$res = Mage::registry ( 'ea_result' );
		
		if ($res != null) {
			if (strpos ( $this->getEabc (), '-' ) === 0) {
				
				$query = array (
						$this->getFilter ()->getRequestVar () => $this->getFilter ()->getResetValue (),
						Mage::getBlockSingleton ( 'page/html_pager' )->getPageVarName () => null, // exclude current page from urls
						'ea_path' => '',
						'ea_c' => '',
						'ea_a' => '',
						'ea_bc' => $this->getEabc () 
				);
				
				return Mage::getUrl ( '*/*/*', array (
						'_current' => true,
						'_use_rewrite' => true,
						'_query' => $query 
				) );
			} else {
				$remPathArr = explode ( '/', $this->getEabc () );
				$catArray = array ();
				$attrArray = array ();
				
				for($i = 0; $i < sizeof ( $remPathArr ); $i ++) {
					if (strpos ( $remPathArr [$i], ":" ) > 0) {
						$attrArray [] = $remPathArr [$i];
					} else {
						$catArray [] = $remPathArr [$i];
					}
				}
				
				$catPath = '';
				$param = '';
				if (sizeof ( $catArray ) > 0) {
					$catPath = $catArray [sizeof ( $catArray ) - 1];
					if (!strpos($catPath, '_')){
						$catPath = implode('/', $catArray);
					}
				}
				if (sizeof ( $attrArray ) > 0) {
					for($j = 0; $j < sizeof ( $attrArray ); $j ++) {
						if ($j == 0) {
							$param = $attrArray [$j];
						} else {
							$param = $param . '&' . $attrArray [$j];
						}
					}
				}
				
				return sizeof ( $catArray ) == 0 ? Mage::getBaseUrl () : Mage::getBaseUrl () . str_replace ( '_', '/', $catPath ) . (strlen ( $param ) === 0 ? '' : '?ea_a=' . $param);
			}
		} else {
			return parent::getRemoveUrl ();
		}
	}
}
