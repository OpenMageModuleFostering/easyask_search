<?php
// Represents a seach advisor attribute.
class EasyAsk_Impl_NavigateAttribute implements EasyAsk_iNavigateAttribute{
	private $m_displayAsLink = false;
	private $m_name;
	private $m_productCount;
	private $m_type;
	private $m_value;
	private $m_nodeString;
	private $m_valueType;
	private $m_minValue;
	private $m_maxValue;
	private $m_minRangeValue;
	private $m_maxRangeValue;
	private $m_rangeRound;

	// Generates a NavigateAttribute based off of an attribute xml node
	function __construct($name, $node){
		$this->m_name = $name;
		$this->m_displayAsLink = $node->displayAsLink;
		$this->m_productCount = $node->productCount;
		$this->m_type = $node->attrType;
		$this->m_nodeString = $node->nodeString;
		$this->m_valueType = $node->valueType;
		$this->m_minValue = isset($node->minValue) ? $node->minValue : 0;
		$this->m_maxValue = isset($node->maxValue) ? $node->maxValue : 0;
		$this->m_minRangeValue = isset($node->minRangeValue) ? $node->minRangeValue : 0;
		$this->m_maxRangeValue = isset($node->maxRangeValue) ? $node->maxRangeValue : 0;
		$this->m_rangeRound = isset($node->rangeRound) ? $node->rangeRound : 0;
		$this->m_value = $node;
	}

	// Whether this attribute should be displayed as a link
	function getDisplayAsLink() { return $this->m_displayAsLink; }
	
	// Gets the name of the attribute
	function getName() { return $this->m_name; }
	
	// The number of products contained within this attribute
	function getProductCount() { return $this->m_productCount; }
	
	// Gets the type of attribute this is. (Color, Size, Product Type, etc)
	function getType() { return $this->m_type; }
	
	// Gets the value contained within this INavigateAttribute. The data used to sort/seperate.
	function getValue() { return $this->m_value; }
	
	// Gets the valueType contained within this INavigateAttribute. The data used to sort/seperate.
	function getValueType() { return $this->m_valueType; }
	
	// Returns ths node location which this attribute is located under.
	public function getNodeString(){ return $this->m_nodeString; }

	// Returns the min Value of the selection.
	public function getMinValue(){ return $this->m_minValue; }

	// Returns the max Value of the selection.
	public function getMaxValue(){ return $this->m_maxValue; }

	// Returns the min Value of the range.
	public function getMinRangeValue(){ return $this->m_minRangeValue; }

	// Returns the max Value of the range.
	public function getMaxRangeValue(){ return $this->m_maxRangeValue; }

	// Returns the round value of the range.
	public function getRangeRound(){ return $this->m_rangeRound; }

	// Returns ths dispaly name of this attribute.
	public function getDisplayName(){
		return $this->m_value->attributeValue;
	}

	private $splitPathSep = "////";
	private $splitValSep = ";;;;";

	// removes references to this node from the path sent in.  
	function removeFromPath($path){
		$nodes = split($splitPathSep, $path);
		$sb;
		$key = getName() . " = '" . getValue() . "'";
		for ($i = 0; $i < sizeof($nodes); $i++)
		{
			if (strpos($nodes[i],"AttribSelect=")){
				$sbNewVal = "";
				$nodeVal = strstr($nodes[i],sizeof("AttribSelect="));
				$vals = split($splitValSep, $nodeVal);
				for ($j = 0; $j < size($vals); $j++){
					if (strcmp($vals[j],$key) != 0){
						if (0 < size($sbNewVal)){
							$sbNewVal = $sbNewVal . ";;;;";
						}
						$sbNewVal = $sbNewVal . $vals[j];
					}
				}
				if (0 < sizeof($sbNewVal)){
					if (0 < sizeof($sb)){
						$sb = $sb . "////";
					}
					$sb = $sb . "AttribSelect=";
					$sb = $sb . $sbNewVal;
				}
			}else{
				if (0 < sizeof($sb)){
					$sb = $sb . "////";
				}
				$sb = $sb . $nodes[i];
			}
		}
		return $sb;
	}
}

?>