<?php
// Contains data about display errors
class EasyAsk_Impl_DisplayFormat
{
	private $m_OutputEngine = 0;
	private $m_Presentation = 0;
	private $m_Error = 0;

	// Builds a display from an appripriate xml node
	function __construct($node){
		if ($node){
			$this->m_OutputEngine = $node->outputEngine;
			$this->m_Presentation = $node->presentation;
			$this->m_Error = $node->error;
		}
	}

	// Returns a numeric representation of the output engine
	function getOutputEngine() { return $this->m_OutputEngine; }
	
	// Returns a numeric representation of the Presentation
	function getPresentation() { return $this->m_Presentation; }
	
	// Returns a numeric representation of any errors that occur
	function getError() { return $this->m_Error; }
	
	// Determines if an error has occured within the presentation
	function isPresentationError() { return $this->getPresentation() == -1; }
	
	// Determines if the current page view is from a redirect or not.
	function isRedirect() { return $this->getError() == 5; }
}

?>