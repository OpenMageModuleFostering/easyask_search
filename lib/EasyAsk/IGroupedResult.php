<?php
interface EasyAsk_iGroupedResult
{
    // Returns a list of all the the categories for the group.
	public function getAllCategoryDetails();
	// Returns a list of all the attribute names contained in the IGroupedResult.
    public function getAttributeNames();
    // Returns the attribute value count
    public function getAttributeValueCount($attrName, $attrValue);
    // Returns a list of the category names contained within the IGroupedResult.
    public function getCategories();
    // Returns detailed suggested category
    public function getDetailedSuggestedCategory();
    // Returns the last row of the group on the current page
	public function getEndRow();
	// Returns a description of the group. This is normally an attribute or category value that is applicible for all the items.
    // For example, if grouping on an attribute such as color, the group description could be 'red' for those items that have color = red.
    // In the case of the unspecified group (that is the set of rows that have no value for the criteria, this can be blank).
    public function getGroupValue();
    // Returns the first row of the group on the current page
    public function getStartRow();
    // Returns a string for the id of the suggested category (common parent)
    public function getSuggestedCategoryID();
    // Returns a string for the suggested category name (common parent) for the categories in the group.
    public function getSuggestedCategoryTitle();
    // Returns the total number of items in the group. This may be greater than or equal to the getNumberOfRows
	public function getTotalNumberOfRows();
	// Returns the data held in the grouped result at a specific position
    public function getCellData($row, $col);
    // Returns the number of rows contained in the result.
    public function getNumberOfRows();
    // Returns the IResultRow held in the IGroupedResult at position "row".
    public function getItem($row);
    
    public function getDetailedAttributeValues($attrName, $displayMode);
}
?>