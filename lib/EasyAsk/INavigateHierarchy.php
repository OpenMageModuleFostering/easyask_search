<?php
interface EasyAsk_iNavigateHierarchy
{
	// The hierarchy node name, that is, the category name corresponding to this node.
    public function getName();
    // The category path from root node to this node. The path is seperated by the standard path delimiter (////).
    // The path can be used to perform a category expand to this node.
    public function getPath();
    // Returns a list of NavigateHierarchy corresponding to the sub nodes of this node. A node will have sub nodes if it is
    // the selected node and that category has children, or if it is a sibling node that is on the path to the selected node.
    public function getSubNodes();
    // Whether this node corresponds to the current suggested category.
    public function isSelected();
    // Returns a list of IDs (as Strings) that correspong to the category named in this object. The list will contain at least
    // 1 entry and possibibly more if there are multiple categories with the same name at the same level. The lsit can be 
    // empty for the toplevel node 'All Products'
    public function getIDs();
}
?>