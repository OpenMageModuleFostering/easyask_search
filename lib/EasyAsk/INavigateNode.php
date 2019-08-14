<?php
interface EasyAsk_iNavigateNode
{
	// Returns the value of the node. In the case of a category, this will return the category name.
    // For an attribute, this will return the value of the attribue (COLOR='Brown') will return brown.
    // For a user search entry, will return whatever the user typed in.
    public function getValue();
    // Returns the full path of this node, including this node.
    public function getPath();
    // Returns the full path of this node without additional information about each node.
    public function getPurePath();
    // Returns the Search Engine Optimimization version of the path of this node.
    public function getSEOPath();
    // Returns the type of this node.
    public function getType();
    // returns ths english representation of this node. 
    // eg: if toString returns attribselect=COLOR = 'Brown', this will return "COLOR = 'BROWN'"
    public function getEnglishName();
    // Returns the label of this node.
    public function getLabel();
}
?>