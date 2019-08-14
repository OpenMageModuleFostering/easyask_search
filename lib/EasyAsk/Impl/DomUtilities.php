<?php
// Contains a list of EasyAsk categories and provides methods to easily access
// the categories and pertaining data for the current search as well as the intial values.
class EasyAsk_Impl_DomUtilities
{
	public static function findAttribute($object, $attribute) {
		$return = null;
		if ($object && sizeof($object) > 0){
			foreach($object->attributes() as $a => $b) {
				if ($a == $attribute) {
					$return = $b;
				}
			}
		}
		if($return) {
			return $return;
		}
	}

}
?>