<?php
/* calcul la différence entre 2 dates */
function calcul_DiffDate($date1, $date2, $format = 2, $ajout = true){
	//if($date1 == '' || $date2 == '') return array(0,0,1);
	if(!is_array($date1)){
		$date1 = array(substr($date1, 0, 4), substr($date1, 4, 2), substr($date1, 6, 2));
	}
	if(!is_array($date2)){
		$date2 = array(substr($date2, 0, 4), substr($date2, 4, 2), substr($date2, 6, 2));
	}
	//if($date1 == $date2) return array(0,0,1);

	//print_r($date1);
	//print_r($date2);

	$date1 = mktime(0, 0, 0, $date1[1], $date1[2], $date1[0]);
	$date2 = mktime(0, 0, 0, $date2[1], $date2[2], $date2[0]);
	$diff = abs($date2 - $date1);

	if($format == 3) return $diff;

	$jour = ceil($diff / (60 * 60 * 24));
	$annee = $mois = 0;
	if($format > 1){
		$annee = floor($jour / 365);
		$jour = $jour % 365;
	}
	if($format > 0){
		$mois = floor($jour / 30);
		$jour = $jour % 30;
	}

	if($ajout) $jour++;

	$result = array($annee, $mois, $jour);

	//print_r($result);

	return $result;
}

function calcul_dateTimestamp ($date, $format = 'mysql') {
	$return = 0;

	if($format == 'mysql' && preg_match('#(\d\d\d\d)-(\d\d)-(\d\d)\s*(\d\d):(\d\d):(\d\d)#', $date, $m)){
		$return = mktime($m[4], $m[5], $m[6], $m[2], $m[3], $m[1]);
	}
	elseif($format == 'fr'){
		$datetime = explode(' ',$datetime);
	    $date = explode('/',$datetime[0]);
	    $time = explode(':',$datetime[1]);

	    $return = mktime($time[0], $time[1], $time[2], $date[1], $date[0], $date[2]);
	}

    return $return;
}



?>