<?php
interface EasyAsk_iFeaturedProducts
{
    // Returns the dataset for the FeaturedProducts
    public function getItems();
    // Returns the current number of products in the FeaturedProducts
    public function getProductCount();
}
?>