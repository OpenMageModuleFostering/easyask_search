<?php
interface EasyAsk_iCarveOut
{
    // Returns the maximum size of the carveout
	public function getMaximum();
	// Returns the current display format of the carveout
    public function getDisplayFormat();
    // Returns the dataset for the carveout
    public function getItems();
    // Returns the current number of products in the carveout
    public function getProductCount();
}
?>