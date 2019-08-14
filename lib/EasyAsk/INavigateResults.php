<?php
// This interface provides access to 'navigation' results. This result set contains three sets of data: 
// 1) a product listing 2) a list of categories related to the products 3) a list of attributes related
// to the products. NOTE: Depending on the query or action performed, category and/or attribute data
// may not be present.
interface EasyAsk_iNavigateResults
{
    public function getReturnCode();
    public function getCatPath();
    // Returns the current list of ResultCategory based on the displaymode
    public function getDetailedCategories($nDisplayMode);
    // Returns the current list of ResultCategory
    public function getDetailedCategoriesFull();
	// Returns initial display limit for categories.
    public function getInitDisplayLimitForCategories();
    // Return a string for the suggested category name (common parent)
    public function getSuggestedCategoryTitle();
    // Returns to total number of pages needed to hold the results of the INavigateResults.
    public function getPageCount();
    // Gets the index of the current page of results that the INavigateResults is displaying.
    public function getCurrentPage();
    public function getIsDrillDown();
    // Gets the total number of items currently contained within the INavigateResults.
    public function getTotalItems();
    // Returns the current results per page.
    public function getResultsPerPage();
    // Returns the index of the first item on the page.
    public function getFirstItem();
    // Returns the index of the last item on the page.
    public function getLastItem();
    // Returns the current sort order being used by INavigateResults.
    public function getSortOrder();
    public function getDataDescriptions();
    // Returns the numerical index of a column.
    public function getColumnIndex($colName);
    // Gets Gets the data contained within a specific location of the results.
    public function getCellData($row, $col);
    // Gets the breadcrumb trail for the current path.
    public function getBreadCrumbTrail();
    // Returns true if the current path is at the top or root node of all products.
    public function getAtTopNode();
    public function getProductsFromGlobalSearch();
    // Returns true if products could not be found in the current context, but were found by modifying the user query.
    public function getItemsFoundByModifyingQuery();
    // Returns true if products/items were found through a secondary search.
    public function getItemsFoundWIthSecondarySearch();
    // Returns the method in which the product listing was obtained.
    public function getProductRetrievalMethod();
    // Returns the method in which the attribute listing was obtained.
    public function getAttributeRetrievalMethod();
    public function getQuestion();
    public function getIsCommand();
    // Returns a NavigateHierarchyNode that corresponds to the current Navigate Hierarchy if it exists A hiearchy exists if the results options requests it. 
    public function getNavigateHierarchy();
    // Returns true if the initial display is limited for attribute names.
    public function isInitialDispLimitedForAttrNames();
    // Returns the initial display limit for attribute names
    public function getInitialDispLimitForAttrNames();
    // Returns a list of attribute names for a specified group. Returns the full list.
    public function getAttributeNamesFull();
    // Returns a list of attribute names of the specified type.
    public function getAttributeNames($filter, $displayMode);
    // Returns true if the initial display is limited for attribute values for the specified attribute name.
    public function isInitialDispLimitedForAttrValues($attrName);
    // Returns the initial display limit. -1 means no limit.
    public function getInitialDispLimitForAttrValues($attrName);
    // Returns a list of NavigateAttribute objects for the specified attribute name. NavigateAttribute objects contain additional information 
    // about each attribute value. Returns the full list.
    public function getDetailedAttributeValuesFull($attrName);
    // Returns a vector of NavigateAttribute objects for the specified attribute name for a specified group. 
    // NavigateAttribute objects contain additional information about each attribute value.
    public function getDetailedAttributeValues($attrName, $displayMode);
    // Returns a vector of the attribute names that are common to the results and normally not displayed. The combination
    // of getAttributeNames and getCommonAttributeNames covers all the attributes for the set.
    public function getCommonAttributeNames($onlySelected);
    // Returns a list of NavigateAttribute objects for the specified common attribute name. NavigateAttribute objects
    // conatin additional information about each attribute value.
    public function getDetailedCommonAttributeValuesFull($attrName);
    // Returns a list of NavigateAttribute objects for the specified common attribute name. NavigateAttribute objects
    // conatin additional information about each attribute value.
    public function getDetailedCommonAttributeValues($attrName, $displayMode);
    public function getCommentary();
    public function getSpellCorrections();
    public function getCorrectedWords();
    public function getCorrection($word);
    public function getRelaxedTerms();
    public function isPresentationError();
    public function isRedirect();
    public function getRedirect();
	public function isGroupedResult();
	// Returns the grouped result set from the results if any. Null otherwise.
	public function getGroupedResult();
	// Returns a row from the currently displayed page.
	public function getRow($pageRow);
	// Returns a list of carveout objects.
	public function getCarveOuts();
	// Returns a ResultsRowGroup that contains the featured products. Null means none.
	public function getFeaturedProducts();
	// Returns a list of possible arrange by by choices. The result set can be arranged by one of these choices.
	// The value GroupedResultSet.GROUP_NO_GROUPING is returned as a choice for not grouping.
    public function getArrangeByChoices();
	// Returns if there is a banner associated with this Category/Attribute
	public function hasBanner($type);
	//Return Banner Information
	public function getBanner($type);
	// Returns if the attribute is a range filter
	public function isRangeFilter($attrName);
}
?>