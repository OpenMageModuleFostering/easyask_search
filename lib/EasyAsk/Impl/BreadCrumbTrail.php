<?php
// Used to keep track of where the INavigateResults has been.
class EasyAsk_Impl_BreadCrumbTrail implements EasyAsk_IBreadCrumbTrail
{
	private $m_fullPath = null;
	private $m_pureCategoryPath = null;
	private $m_navNodes = array();

	// Builds the Breadcrumbtrail from top node to the provided xml node, to the Category node
	function __construct($node){
		if ($node != null){
			$this->m_fullPath = $node->fullPath;
			$this->m_pureCategoryPath = $node->pureCategoryPath;
			$nodes = $node->navPathNodeList;
			if ($nodes){
				foreach($nodes as $navNode){
					$this->m_navNodes[] = new EasyAsk_Impl_NavigateNode($navNode);
				}
			}
		}
	}

	// Returns the full path from the top node to the current node location.
	function getFullPath(){ return $this->m_fullPath; }
	
	// Returns the path from the top node to the category node.
	function getPureCategoryPath() { return $this->m_pureCategoryPath; }

	// Returns the path being used by the current search.
	function getSearchPath() { return $this->m_navNodes; }
}

?>