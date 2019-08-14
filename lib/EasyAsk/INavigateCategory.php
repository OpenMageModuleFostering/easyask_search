<?php
interface EasyAsk_iNavigateCategory
{
	// Returns a list of ids (as Strings) that correspond to the category named in this object 
	// The list will contain at least 1 entry and possibly more if there are multiple categories with the same name at the same level. 
	// The ids will be only from categories that are referenced by products in the result set, that is, 
	// if there are 5 categories with the same name, but only 3 of them are referenced in the result set, 
	// then the list will contain 3 Strings not 5 
    public function getIDs();
    // Returns the category name.
    public function getName();
    // Returns the number of products that have this category. Returns -1 if the product count is unknown
    public function getProductCount();
    // Returns a list of NavigateCategory corresponding to the subcategories of this node
    public function getSubCategories();
    // Returns a string corresponding to the path segment for this category. See NavigateNode.toString()
    public function getNodeString();
    // Returns a string corresponding to the full path to this category
    public function getSEOPath();
}
?>