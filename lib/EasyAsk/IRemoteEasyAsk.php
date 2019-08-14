<?php
interface EasyAsk_iRemoteEasyAsk
{
    // Returns INavigateResults for when the user performs a search.
	public function userSearch($path, $question);
	// Returns INavigateResults for when the user does a page operation
    public function userPageOp($path, $curPage, $pageOp);
    // Returns INavigateResults for when the user changes results pages
    public function userGoToPage($path, $page);
    // Returns INavigateResults for a click on a category
    public function userCategoryClick($path, $cat);
    // Returns INavigateResults for a click on the BreadCrumb Path
    public function userBreadCrumbClick($path);
    // Returns INavigateResults for a click on an attribute value
    public function userAttributeClick($path, $attr);
    // Sets the options for the RemoteEasyAsk
    public function setOptions($val);
    // Gets the current Options being used by the RemoteEasyAsk
    public function getOptions();
}
?>