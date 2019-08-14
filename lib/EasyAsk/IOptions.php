<?php
interface EasyAsk_iOptions
{
    // Sets the dictionary file (.dxp) name that this EasyAsk instance will reference.
	public function setDictionary($name);
	// Gets the dictionary file name that this EasyAsk instance is referencing.
    public function getDictionary();
    // Sets the boolean value of navigation hierarchy for this EasyAsk instance.
    public function setNavigateHierarchy($val);
    // Gets the boolean value of navigation hierarchy for this EasyAsk instance.
    public function getNavigateHierarchy();
    // Sets the boolean value of subcategories for this EasyAsk instance.
    public function setSubCategories($val);
    // Gets the boolean value of subcategories for this EasyAsk instance.
    public function getSubCategories();
    // Sets the boolean value of top level products for this EasyAsk instance.
    public function setToplevelProducts($val);
    // Gets the boolean value of top level products for this EasyAsk instance.
    public function getToplevelProducts();
    // Sets the number of results to be displayed per page of the EasyAsk display.
    public function setResultsPerPage($val);
    // Gets the current number of results to be displayed per page.
    public function getResultsPerPage();
    // Sets the order which governs how the results will be sorted (by price, by name, etc)
    public function setSortOrder($val);
    // The current sort order being implemented by the EasyAsk instance.
    public function getSortOrder();
    // Sets the boolean value to return SKUs for this EasyAsk instance.
    public function setReturnSKUs($val);
    // Gets the boolean value to return SKUs for this EasyAsk instance.
    public function getReturnSKUs();
    // Sets the grouping term
    public function setGrouping($val);
    // Gets the grouping term
    public function getGrouping();
    // Sets the callout params value
    public function setCallOutParam($val);
    // Gets the call out params value
    public function getCallOutParam();
	// Sets the groupid parameter for the current instance.
	public function setGroupId($groupId);
	// Gets the groupid parameter for the current instance
	public function getGroupId();
	// Sets the customerid parameter for the current instance.
	public function setCustomerId($customerId);
	// Gets the customerid parameter for the current instance
	public function getCustomerId();
	// Sets the customer parameter for the current instance.
	public function setCustomer($customer);
	// Gets the customer parameter for the current instance
	public function getCustomer();
}
?>