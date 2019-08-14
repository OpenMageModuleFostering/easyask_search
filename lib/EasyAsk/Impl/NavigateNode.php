<?php
// A specific node within the product heirarchy.
class EasyAsk_Impl_NavigateNode implements EasyAsk_iNavigateNode
{
	private $m_value;
	private $m_path;
	private $m_purePath;
	private $m_SEOPath;
	private $m_type;
	private $m_englishName;

	// Builds a node based off of the xml node provided
	function __construct($node){
		$this->m_value = $node->value;
		$this->m_path = $node->path;
		$this->m_purePath = $node->purePath;
		$this->m_SEOPath = isset($node->seoPath)?$node->seoPath:'';
		$this->m_type = $node->navNodePathType;
		$this->m_englishName = $node->englishName;
	}

	// Gets the value contained in the node.
	function getValue() { return $this->m_value; }
	
	// Gets the path to the node in the hierarchy
	function getPath() { return $this->m_path; }
	
	// Gets the pure path to the node in the hierarchy
	function getPurePath() { return $this->m_purePath; }
	
	// Gets the Search Engine Optimization path for the node 
	function getSEOPath() { return $this->m_SEOPath; }
	
	// Returns the type of node
	function getType() { return $this->m_type; }
	
	// Returns the natural language english version of the node name
	function getEnglishName() { return $this->m_englishName; }
	
	// Returns the label associated with this node
    // eg: Item Category, User Search, Color, etc
    public function getLabel(){
    	if ($this->m_type == 2){
    		substr($this->m_englishName, 1, (strrpos("=", $this->m_englishName) - 1));
    	} else if ($this->m_type == 1){
    		return "Item Category";
    	} else if ($this->m_type == 3){
    		return "User Search";
    	}
    	return null;
    }
}

?>