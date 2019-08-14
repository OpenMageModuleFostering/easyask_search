<?php
// Contains the info which pertain to a specific EasyAsk attribute.
class EasyAsk_Impl_AttributeInfo
{
    private $m_xmlNode = null;
    private $m_name;
    private $m_isLimited = false;
    private $m_limit = -1;
    private $m_initialList = null;
    private $m_fullList = null;
    private $m_attrType = 11;
    private $m_isRangeFilter = false;
	
    // Creates the AttributeInfo for a node and pulls 
	function __construct($node){
		$this->m_xmlNode = $node;
		$this->m_name = $node->name;
		$this->m_isLimited = isset($node->isInitDispLimited)?$node->isInitDispLimited:false;
		$this->m_limit = isset($node->initDispLimit)?$node->initDispLimit:'';
		$this->m_attrType = isset($node->attributeValueList[0]->attrType)?
				$node->attributeValueList[0]->attrType : 11;
		$this->m_fullList = isset($node->attributeValueList)?
					$this->formList($this->m_xmlNode->attributeValueList):null;
		$this->m_initialList = isset($node->initialAttributeValueList)?
					$this->formList($this->m_xmlNode->initialAttributeValueList):null;
		$this->m_isRangeFilter = isset($node->attributeValueList)?
					$this->getRangeAttribute($this->m_xmlNode->attributeValueList):null;
	}

	// Creates a list of INavigateAttributes from a list of XMLnodes. 
	function formList($attrVals){
		$result = array();
		if ($attrVals){
			foreach ($attrVals as $attrVal){
				$result[] = new EasyAsk_Impl_NavigateAttribute($this->m_name,$attrVal);
			}
		}
		return $result;
	}

	// Returns the initial list of NavigateAttributes that correspond to this xml node
	function getInitialList() {
		return $this->m_initialList; 
	}

	// Returns the full and current list of NavigateAttributes corresponding to this xmlNode
	function getFullList() { 
		return $this->m_fullList; 
	}
	
	// Returns the name of the attribute.
	function getName() { 
		return $this->m_name; 
	}
	
	// Returns whether this attribute is limited for certain displays.
	function getIsLimited() { 
		return $this->m_isLimited; 
	}
	
	// Returns the size limit of this attribute
	function getLimit() { 
		return $this->m_limit; 
	}
	
	// Returns the attribute type
	function getAttrType() { 
		return $this->m_attrType; 
	}
	
	// Returns the value of range filter
	function getIsRangeFilter() {
		return $this->m_isRangeFilter;
	}  

	// Returns if the attribute is a range attribute
	function getRangeAttribute($attrVals) {
		$result = false;
		if ($attrVals){
			foreach ($attrVals as $attrVal){
				$attribute = new EasyAsk_Impl_NavigateAttribute($this->m_name,$attrVal);
//				if (isset($attribute->getValueType())){
					$result = ($attribute->getValueType() == 2) ? true : false;
//				}
			}
		}
		return $result;		
	}
}

?>