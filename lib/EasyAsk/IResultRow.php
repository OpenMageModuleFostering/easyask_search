<?php
interface EasyAsk_iResultRow
{
    // Gets the data in a column of the row
    public function getCellData($col);
    // The number of columns in the row.
    public function size();
}
?>