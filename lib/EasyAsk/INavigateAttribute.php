<?php
interface EasyAsk_iNavigateAttribute
{
	// Returns true if we recommend showing this attribute as a link.
    public function getDisplayAsLink();
    // Gets the name of this attribute. This is for display/user purposes. It is not used when sorting through the actual data.
    public function getName();
    // Gets the amount of products which this attribute applies to.
    public function getProductCount();
    // Gets the type of attribute this is. (Color, Size, Product Type, etc)
    public function getType();
	// Gets the valueType contained within this INavigateAttribute. The data used to sort/seperate.
	function getValueType();
    // Gets the value contained within this INavigateAttribute. The data used to sort/seperate.
    public function getValue();
    // Returns ths node location which this attribute is located under.
    public function getNodeString();
    // Removes references to this node from the given path.
    public function removeFromPath($path);
	// Returns the min Value of the selection.
	public function getMinValue();
	// Returns the max Value of the selection.
	public function getMaxValue();
	// Returns the min Value of the range.
	public function getMinRangeValue();
	// Returns the max Value of the range.
	public function getMaxRangeValue();
	// Returns the round value of the range.
	public function getRangeRound();
	// Returns ths dispaly name of this attribute.
	public function getDisplayName();
}
?>