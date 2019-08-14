<?php
// Implements IDataDescription
// Contains various information about the current data in a node
class EasyAsk_Impl_DataDescription implements EasyAsk_iDataDescription{

	private $m_colType;
	private $m_htmlType;
	private $m_displayable;
	private $m_decoded;
	private $m_format;
	private $m_tagName;
	private $m_colName;

	// Builds the DataDescription of a certain node
	function __construct($node){
		$this->m_colType = $node->colType;
		$this->m_htmlType = $node->htmlType;
		$this->m_displayable = isset($node->displayable)?$node->displayable:'';
		$this->m_decoded = $node->decoded;
		$this->m_format = isset($node->format)?$node->format:'';
		$this->m_tagName = $node->tagName;
		$this->m_colName = $node->columnName;
	}

	// Returns whether the data is displayable or not.
	function getDisplayable() { return $this->m_displayable; }
	
	// Returns whether the data is decoded or not.
	function getDecoded() { return $this->m_decoded; }
	
	// Returns the column type of the current data.
	function getColType() { return $this->m_colType; }
	
	// Returns the HTML type of the current data.
	function getHTMLType() { return $this->m_htmlType; }
	
	// Returns the format type of the current data.
	function getFormat() { return $this->m_format; }
	
	// Returns the tag name of the current data.
	function getTagName() { return $this->m_tagName; }
	
	// Returns the column name of the current data.
	function getColName() { return $this->m_colName; }
} 
?>