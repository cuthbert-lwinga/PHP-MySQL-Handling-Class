<?PHP
class  Utility{
	public static function isArrayofArrays($array) {
    $isArrayofArrays = !empty($array) && is_array($array) && count(array_filter($array, 'is_array')) === count($array);
    return $isArrayofArrays;
}
}
?>