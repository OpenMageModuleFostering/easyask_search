<?php
class EasyAsk_Impl_AttributesInfo
{
	private $m_bInitialDispLimitedForAttrNames = false;
	private $m_initialDispLimitForAttrNames = 1;
	private $m_initialAttributeNames = array();
	private $m_attributes = array();

	// Builds a list of attributesinfo based off of an appropriate xml node.
	function __construct($node){
		if ($node){
			$this->m_attributes = $this->getAttributeInfo($node);
			$this->m_bInitialDispLimitedForAttrNames = isset($node->isInitDispLimited)?$node->isInitDispLimited:false;
			$this->m_initialDispLimitForAttrNames = isset($node->initDispLimit)?$node->initDispLimit:'';
			$initLists = isset($node->initialAttrNameOrder)?$node->initialAttrNameOrder:null;
			if ($initLists != null){
				foreach ($initLists as $initList){
					$attrType = $initList->attrType;
					$initNames = $initList->attributeName;
					if (0 < sizeof($initNames)){
						$vals = array();
						foreach ($initNames as $name){
							$vals[] = $name;
						}
						$this->m_initialAttributeNames[$attrType] = $vals;
					}
				}
			}
		}
	}

	// Returns a list of AttributeInfo that contains all the attributes contained within an xml node
	private function getAttributeInfo($node){
		$results = array();
		if ($node){
			$attrs = isset($node->attribute) ? $node->attribute : $node;
			foreach ($attrs as $attr){
				$results[] = new EasyAsk_Impl_AttributeInfo($attr);
			}
		}
		return $results;
	}

	// Returns whether the attribute list is limited to the initial display
	public function isInitialDispLimitedForAttrNames(){
		return $this->m_bInitialDispLimitedForAttrNames;
	}

	// Returns initial display limit for the attribute list.
	public function getInitialDispLimitForAttrNames() {
		return $this->m_initialDispLimitForAttrNames;
	}

	// Returns a list attribute names which correspond to a certain attribute type.
	public function getInitialDisplayList($attrType){
		return $this->m_initialAttributeNames[$attrType];
	}

	// Returns a list of attribute names for a certain display mode which are also of a certain attribute type.
	public function getAttributeNames($attrFilter, $displayMode){
		if ($displayMode == 1){
			return $this->m_initialAttributeNames[$attrFilter];
		}else{
			$result = array();
			foreach ($this->m_attributes as $attrInfo){
				if (0 != ($attrInfo->getAttrType() & $attrFilter)){
					$result[] = $attrInfo->getName();
				}
			}
			return $result;
		}
	}

	// Returns the AttributeInfo object for a certain attribute.
	private function getAttrInfo($attrName){
		foreach ($this->m_attributes as $attrInfo){
			if (strcmp($attrName, $attrInfo->getName()) == 0){
				return $attrInfo;
			}
		}
		return null;
	}

	// Returns whether a specific attribute is limited the initial display or not.
	public function isInitialDispLimitedForAttrValues($attrName){
		$attrInfo = $this->getAttrInfo($attrName);
		return null == $attrInfo ? false : $attrInfo->getIsLimited();
	}

	// Returns the initial display limit integer for a specific attribute.
	public function getInitialDispLimitForAttrValues($attrName){
		$attrInfo = $this->getAttrInfo($attrName);
		return null == $attrInfo ? -1 : $attrInfo->getLimit();
	}
	
	// Returns if the attribute is a range filter.
	public function isRangeFilter($attrName){
		$attrInfo = $this->getAttrInfo($attrName);
		return null == $attrInfo ? -1 : $attrInfo->getIsRangeFilter();
	} 

	// Returns a list of NavigateAttributes for a certain display mode that correspond to a certain attribute name.
	public function getDetailedAttributeValues($attrName, $displayMode){
		$result = array();
		$attrInfo = $this->getAttrInfo($attrName);
		if (null != $attrInfo){
			if($displayMode == 1 && !$attrInfo->getIsLimited()){
				$displayMode = 0;
			}
			$result = $displayMode == 0 ? $attrInfo->getFullList() : $attrInfo->getInitialList();
		}
		return $result;
	}

}
?>