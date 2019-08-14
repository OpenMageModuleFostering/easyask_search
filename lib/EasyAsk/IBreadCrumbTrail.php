<?php
interface EasyAsk_iBreadCrumbTrail
{
    // Returns the full path to the current node location.
	public function getFullPath();
	// Returns the path to the category node.
    public function getPureCategoryPath();
    // Returns the path the search used to get to the current node location.
    public function getSearchPath();
}
?>