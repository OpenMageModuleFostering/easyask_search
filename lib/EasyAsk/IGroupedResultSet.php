<?php
interface EasyAsk_iGroupedResultSet
{
    // Returns the index of the last group on the current page
	public function getEndGroup();
	// Retrieves a group based on the index
    public function getGroup($i);
    // Returns the criteria of the GroupedResultSet, either the name of the attribute or current category level.
    public function getGroupCriteria();
    // Returns the type of search
    public function getGroupCriteriaType();
    // Returns the current maximum number of items per group (can return GROUP_RETURN_ALL)
    public function getMaximumRowsPerGroup();
    // Returns the total number of groups (including the group with no criteria)
	public function getNumberOfGroups();
	// Returns the number of pages needed to diplay all the groups
    public function getPageCount();
    // Returns the index of the first group on the current page
    public function getStartGroup();
    // Returns the total number of items in the set.
    public function getTotalNumberOfRows();
    // Returns the node string for a specific group within the set.
    public function getNodeString($group);
}
?>