<?php
// Contains data about banner
class EasyAsk_Impl_Banner
{
	private $m_Type = 0;
	private $m_BlockIdentifier = '';
	private $m_TriggerType = 0;
	private $m_TriggerValue = '';
	
	// Builds a display from an appripriate xml node
	function __construct($node){
		if ($node){
			$this->m_Type = $node->type;
			$this->m_BlockIdentifier = $node->blockIdentifier;
			$this->m_TriggerType = $node->triggerType;
			$this->m_TriggerValue = $node->triggerValue;
		}
	}

	// Returns a numeric representation of the type
	function getType() { return $this->m_Type; }

	// Returns a string representation of the blockIdentifier
	function getBlockIdentifier() { return $this->m_BlockIdentifier; }

	// Returns a numeric representation of the trigger Type
	function getTriggerType() { return $this->m_TriggerType; }

	// Returns a string representation of the trigger Value
	function getTriggerValue() { return $this->m_TriggerValue; }
}

?>