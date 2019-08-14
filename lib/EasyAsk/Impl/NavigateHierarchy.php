<?php
// Represents a search advisor category hierarchy node.
class EasyAsk_Impl_NavigateHierarchy implements EasyAsk_iNavigateHierarchy{
	private $m_name;
	private $m_path;
	private $m_subNodes;
	private $m_isSelected;
	private $m_ids = null;

	// Builds the navigate hierarchy based off of an appropriate xml node
	function __construct($node){
		$this->m_name = $node;
		$this->m_path = $node->navHierPath;
		$this->m_isSelected = $node->isSelected;
		$subNodes = $node->navSubNodes->navHierNode;
		
		$this->m_subNodes = array();
		if ($subNodes != null){
			foreach ($subNodes as $sub){
				$this->m_subNodes[] = new EasyAsk_Impl_NavigateHierarchy($sub);
			}
		}
		$idList = $node->ids;
		$this->m_ids = array();
		if ($idList){
			$ids = split(',', $idList);
			foreach($ids as $id){
				$this->m_ids[] = $id;
			}
		}
	}

	// The hierarchy node name, that is, the category name corresponding to this node.
	function getName() { return $this->m_name; }
	
	// The category path from root node to this node. The path is seperated by the standard path delimiter (////).
    // The path can be used to perform a category expand to this node.
	function getPath() { return $this->m_path; }
	
	// Returns a list of NavigateHierarchy corresponding to the sub nodes of this node. A node will have sub nodes if it is
    // the selected node and that category has children, or if it is a sibling node that is on the path to the selected node.
	function getSubNodes() { return $this->m_subNodes; }
	
	// Whether this node corresponds to the current suggested category.
	function isSelected() { return $this->m_isSelected; }
	
	// Returns a list of IDs (as Strings) that correspong to the category named in this object. The list will contain at least
    // 1 entry and possibibly more if there are multiple categories with the same name at the same level. The list can be 
    // empty for the toplevel node 'All Products'
	function getIDs() { return $this->m_ids; }
}

?>