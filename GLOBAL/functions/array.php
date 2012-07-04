<?
/**
 * Créer un tableau associatif à parti d'un tableau de clefs et de valeurs
 * 
 * @param array $Keys
 * @param array $Values
 * @return array 
 */
function array_keys_values($Keys, $Values)
{
    $array = array();
    for($i = 0; $i < count($Keys); $i++)
    {
        $array[$Keys[$i]] = $Values[$i];
    }
    return $array;
}


/**
 * Insert un élément dans un tableau
 * 
 * @param array $array
 * @param int $index
 * @param mixed $value 
 */
function array_insert(&$array, $index, $value)
{
    for( $i = count($array) - 1; $i >= $index; --$i )
    {
        $array[ $i + 1 ] = $array[ $i ];
    }
    $array[$index] = $value;
}
?>