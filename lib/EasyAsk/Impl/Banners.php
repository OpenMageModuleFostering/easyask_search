<?php
// Contains data about all the banners
class EasyAsk_Impl_Banners
{
	private $m_banners = array();
	
	// Builds a list of attributesinfo based off of an appropriate xml node.
	function __construct($node){
		if ($node){
			$this->m_banners = $this->getBanners($node);
		}
	}
	
	// Returns a list of Banner objects that contains all the attributes contained within an xml node
	private function getBanners($node){
		$results = array();
		if ($node){
			$banners = isset($node->banner) ? $node->attribute : $node;
			foreach ($banners as $banner){
				$results[] = new EasyAsk_Impl_Banner($banner);
			}
		}
		return $results;
	}
	
	// Returns the Banner object for a certain type.
	public function getBanner($type){
		foreach ($this->m_banners as $banner){
			if (strcmp($type, $banner->getType()) == 0){
				return $banner;
			}
		}
		return null;
	}
	
	// Returns the Banner object for a certain type.
	public function hasBanner($type){
		foreach ($this->m_banners as $banner){
			if (strcmp($type, $banner->getType()) == 0){
				return true;
			}
		}
		return false;
	}
	
}
