<?php
/* définit si une variable est défini et non vide */
function is_set($var, $index = false){
  $return = false;

  if($index){
    $return = isset($var[$index]) && is_string($var[$index]) && trim($var[$index]) !== '';
  }
  else{
    $return = isset($var) && is_string($var) && trim($var) !== '';
  }

  return $return;
}


function is_email($str){
    return preg_match('/'.User::$Pattern['email'].'/i', $str);
}


function is_date($str, $separ = '\/'){
    return preg_match('#^[0-9]{2}'.$separ.'[0-9]{2}'.$separ.'[0-9]{4}$#', $str);
}


function is_datetime($str, $separ = '\/'){
    return preg_match('#^[0-9]{2}'.$separ.'[0-9]{2}'.$separ.'[0-9]{4} [0-9]{2}:[0-9]{2}$#', $str);
}

?>