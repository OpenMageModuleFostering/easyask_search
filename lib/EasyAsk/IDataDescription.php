<?php
interface EasyAsk_iDataDescription
{
	// Returns whether the data is displayable or not.
    public function getDisplayable();
    // Returns whether the data is decoded or not.
    public function getDecoded();
    // Returns the column type of the current data.
    public function getColType();
    // Returns the HTML type of the current data.
    public function getHTMLType();
    // Returns the format type of the current data.
    public function getFormat();
    // Returns the tag name of the current data.
    public function getTagName();
    // Returns the column name of the current data.
    public function getColName();
}
?>