<?php
/****************************************************************/
/***************** ENCODAGE *************************************/
/****************************************************************/


function encode($str){
    return htmlentities($str, ENT_QUOTES, 'UTF-8', false);
}


function display($str){
    return $str;
}


function decode($str){
    return iconv('UTF-8', 'ISO-8859-15', $str);
}


/* supprime les accents */
function format_stripAccents($string){
	return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}


/****************************************************************/
/***************** DATES ****************************************/
/****************************************************************/


/* Format la différence de date */
function format_diffDate($wDiff){
	$wtDiff = array();
	//print_r($wDiff);
	if($wDiff[0] > 0){
		$wtDiff[] = intval($wDiff[0]).' an'.($wDiff[0] > 1 ? 's' : '');
	}
	if($wDiff[1] > 0){
		$wtDiff[] = ($wDiff[1]).' mois';
	}
	if($wDiff[2] > 0){
		$wtDiff[] = intval($wDiff[2]).' jour'.($wDiff[2] > 1 ? 's' : '');
	}
	$wRetour = implode(', ',$wtDiff);
	$wRetour = str_ireplace(', ', ' et ', $wRetour);
	
	return $wRetour;
}

/* Remplie une date avec la date actuelle */
function format_rempliDate($pAnnee, $pMois, $pJour){
	$dateref = array(date('Y'), date('m'), date('d'));

	if($pAnnee) $dateref[0] = $pAnnee;
	if($pMois) $dateref[1] = $pMois;
	if($pJour) $dateref[2] = $pJour;

	return $dateref;
}

function format_dateAffichage($date, $bHour = true, $in = 'mysql', $out = 'fr'){
	$formatIn = "(\d{4})-(\d{2})-(\d{2})";
	$formatOut = "$3/$2/$1";

	if($bHour){
		$formatIn .= " (\d{2}):(\d{2})";
		$formatOut .= " $4:$5";
	} 

	return preg_replace('/'.$formatIn.'/i', $formatOut, $date);
}




function date_difference ($date_recent, $date_old) {
    return date_to_timestamp($date_recent) - date_to_timestamp($date_old);
}

function date2fr($date, $separ = '/') {
	if($date == '0000-00-00')
		return '';
	else
		return preg_replace(
			"/([0-9]{4})-([0-9]{2})-([0-9]{2})/i",
			"$3".$separ."$2".$separ."$1",
			$date
		);
}

function date2mysql($date, $separ = '\/'){
    return preg_replace(
        "/([0-9]{2})".$separ."([0-9]{2})".$separ."([0-9]{4})/i",
        "$3-$2-$1",
        $date
    );
}

function date2us($date, $separ = false) 
{
    return preg_replace(
        "/([0-9]{2})-([0-9]{2})-([0-9]{4})/i",
        "$3".$separ."$2".$separ."$1",
        $date
    );
}


function calculerAge_complet($date)
{
	$annees = 'XX';

    if($date != '' && $date != '0000-00-00'){
            list($annee, $mois, $jour) = explode('-', $date);
            $today['mois'] = date('n');
            $today['jour'] = date('j');
            $today['annee'] = date('Y');
            $annees = $today['annee'] - $annee;
            if ($today['mois'] <= $mois) {
                    if ($mois == $today['mois']) {
                      if ($jour > $today['jour'])
                                    $annees--;
                      }
                    else
                      $annees--;
            }
    }
    return $annees;
}

function convertMinutes($lesMinutes)
{ 
    $heures = floor( $lesMinutes / 60 );
    $minutes = $lesMinutes % 60 ;
    $heures = sprintf( "%02s" , $heures );
    $minutes = sprintf( "%02s" , $minutes );

    return( $heures . ":" . $minutes );
}


function format_timeAgo($cur_time){
	if($cur_time == '') return ''; 

    $time_ = time() - $cur_time;

    $seconds =$time_;
    $minutes = round($time_ / 60);
    $hours = round($time_ / 3600);
    $days = round($time_ / 86400);
    $weeks = round($time_ / 604800);
    $months = round($time_ / 2419200);
    $years = round($time_ / 29030400);

    //Seconds
    if($seconds <= 60){

       $time="$seconds seconds ago";   

    //Minutes    
    }else if($minutes <= 60){

       if($minutes == 1){
       $time="one minute ago";
       }else{
       $time="$minutes minutes ago";
       }

    //Hours
    }else if($hours <= 24){

      if($hours == 1){
      $time="one hour ago";
      }else{
      $time="$hours hours ago";
      }

    //Days 
    }else if($days <= 7){

       if($days == 1){
       $time="one day ago";
       }else{
       $time="$days days ago";
       }

    //Weeks
    }else if($weeks <= 4){

      if($weeks == 1){
      $time="one week ago";
      }else{
      $time="$weeks weeks ago";
      }

    //Months  
    }else if($months <= 12){

      if($months == 1){
      $time="one month ago";
      }else{
      $time="$months months ago";
      }

    //Years 
    }else{  

      if($year == 1){
      $time="one year ago";
      }else{
      $time="$year years ago";
      }  

    }
    return $time;
}


/****************************************************************/
/***************** NOMBRES **************************************/
/****************************************************************/


/* format la taille de fichier */
function format_tailleFichier($taille,$pDec=0){
	if ($taille >= 1073741824) {
		$taille = round($taille / 1073741824, $pDec) . " Go";
	}
	elseif ($taille >= 1048576) {
		$taille = round($taille / 1048576, $pDec) . " Mo";
	}
	elseif ($taille >= 1024) {
		$taille = round($taille / 1024, $pDec) . " Ko";
	}
	else {
		$taille = $taille . " o";
	} 
	return $taille;
}


/* format un nombre/montant */
function format_nombre($sVal, $bNegatif = true, $bEspace = false, $bVide = true, $nFlottant = 2, $nEntier = false, $bFormatFlottant = true){
	$wVal = $sVal;
	
	if(strlen($wVal) > 0 || !$bVide){
		$bNegatif = $bNegatif && strpos($wVal, '-') >= 0;
		/* On nétoie la chaine */
		$wVal = str_replace(' ','', $wVal);
		$wVal = str_replace('-','', $wVal);
		$wVal = str_replace('.',',', $wVal);
		$pVirgule = strpos($wVal, ',');
		$wFlottant = '';
		
		/* On récupère la partie entière */
		$pFinEntier = $pVirgule > -1 ? $pVirgule : strlen($wVal);
		if($nEntier && $pFinEntier > $nEntier) $pFinEntier = $nEntier;
		$wEntier = substr($wVal, 0, $pFinEntier);
		
		/*echo '___________'.PHP_EOL;
		echo $wVal.PHP_EOL;
		echo $pVirgule.PHP_EOL;
		echo $pFinEntier.PHP_EOL;
		echo $wEntier.PHP_EOL;
		echo '___________'.PHP_EOL;*/
	
		if(strlen($wEntier) == 0 || !is_int($wEntier)) $wEntier = '0';
		else $wEntier = intval($wEntier);

		/* Si nécéssite séparateur de millier */
		if($bEspace){
			$i = strlen($wEntier);
			while($i > 3){
				$wPrec = substr($wEntier, 0, i - 3);
				$Suiv = substr($wEntier, i - 3);
				$wEntier = $wPrec . ' ' . $Suiv;
				$i -= 3;
			}
		}
		
		/* Nombre flottant de base */
		for($i = 0; $i < $nFlottant && $nFlottant > 0 && $bFormatFlottant; $i++){
			$wFlottant = $wFlottant . '0';
		}
		
		/* Si y'a une virgule */
		if($nFlottant > 0 && $pVirgule > -1){
			/* On récupère la partie flottante */
			$wFlottant = substr($wVal, $pVirgule + 1);
			$wFlottant = str_replace(',', '', $wFlottant);
			
			/* Si plus de nombre flottant que nécessaire */
			if(strlen($wFlottant) >= $nFlottant)
				$wFlottant = substr($wFlottant, 0, $nFlottant);
			/* Si pas assez de nombre flottant */
			else if($bFormatFlottant){
				for($i = strlen($wFlottant); $i < $nFlottant; $i++){
					$wFlottant = $wFlottant . '0';
				}
			}
		}
		$wVal = $wEntier;
		
		if($nFlottant > 0 && ($wFlottant != '' || $pVirgule > -1))  $wVal = $wVal . ',' . $wFlottant;
		if($bNegatif){
			if($sVal == '-') {
				$wVal = $bFormatFlottant ? '' : '-';
			}
			else if($wVal != 0 || !$bFormatFlottant) $wVal = '-' . $wVal;
		}
	}
	
	return $wVal;
};


/* Définie une homethie avec une taille et une taille max */
function format_homothetie($tabSize,$tabSizeMax){
	$diffW = 0;
	$diffH = 0;
	
	if($tabSize[0] > $tabSizeMax[0]) {
		$diffW = $tabSize[0] - $tabSizeMax[0];
	}
	if ($tabSize[1] > $tabSizeMax[1]) {
		$diffH = $tabSize[1] - $tabSizeMax[1];
	}
	
	// Si les dimensions sont supérieur aux dimensions maximum
	if($diffW != 0 || $diffH != 0)
	{
		// Redimensionne en fonction de la difference de taille
		if( $diffW > $diffH ) {
			$ratio = $tabSizeMax[0] / $tabSize[0];
			$tabSize[0] = $tabSizeMax[0];
			$tabSize[1] = round($tabSize[1] * $ratio, 0);
		}
		else{
			$ratio = $tabSizeMax[1] / $tabSize[1];
			$tabSize[1] = $tabSizeMax[1];
			$tabSize[0] = round($tabSize[0] * $ratio, 0);
		}
		// Si ce n'est pas suffisant, on redimensionne en fonction de la taille minimum
		if( $tabSize[0] > $tabSizeMax[0] || $tabSize[1] > $tabSizeMax[1]){
			if( $tabSizeMax[0] < $tabSizeMax[1] ){
				$ratio = $tabSizeMax[0] / $tabSize[0];
				$tabSize[0] = $tabSizeMax[0];
				$tabSize[1] = round($tabSize[1] * $ratio, 0);
			}
			else{
				$ratio = $tabSizeMax[1] / $tabSize[1];
				$tabSize[1] = $tabSizeMax[1];
				$tabSize[0] = round($tabSize[0] * $ratio, 0);
			}
		}
	}
	return $tabSize;
}


/****************************************************************/
/***************** DATES ****************************************/
/****************************************************************/


/* format le password */
function format_password($password) 
{ 
    $salt = sha1('INFINITY'); 
    $salt = substr($salt, 0, 4); 
    $hash = base64_encode( sha1($password . $salt, true) . $salt ); 
    return $hash; 
}

function format_randomPasword($pw_length)
{
        $pass = NULL;
        $charlist = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrstuvwxyz0123456789';
        $ps_len = strlen($charlist);
        mt_srand((double)microtime()*1000000);

        for($i = 0; $i < $pw_length; $i++) {
                $pass .= $charlist[mt_rand(0, $ps_len - 1)];
        }
        return ($pass);
}


/* formate les bbCodes */
function format_bbCode($t)
// remplace les balises BBCode par des balises HTML
{
   // barre horizontale
   $t=str_replace("[/]", "<hr width=\"100%\" size=\"1\" />", $t);
   $t=str_replace("[hr]", "<hr width=\"100%\" size=\"1\" />", $t);

   // gras
   $t=str_replace("[b]", "<strong>", $t);
   $t=str_replace("[/b]", "</strong>", $t);

   // italique
   $t=str_replace("[i]", "<em>", $t);
   $t=str_replace("[/i]", "</em>", $t);

   // soulignement
   $t=str_replace("[u]", "<u>", $t);
   $t=str_replace("[/u]", "</u>", $t);

   // alignement centré
   $t=str_replace("[center]", "<div style=\"text-align: center\">", $t);
   $t=str_replace("[/center]", "</div>", $t);

   // alignement à droite
   $t=str_replace("[right]", "<div style=\"text-align: right\">", $t);
   $t=str_replace("[/right]", "</div>", $t);

   // alignement justifié
   $t=str_replace("[justify]", "<div style=\"text-align: justify\">", $t);
   $t=str_replace("[/justify]", "</div>", $t);

   // couleur
   $t=str_replace("[/color]", "</span>", $t);
   $regCouleur="/\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]/i";
   $t=preg_replace($regCouleur, "<span style=\"color: \\1\">", $t);

   // taille des caractères
   $t=str_replace("[/size]", "</span>", $t);
   $regCouleur="/\[size= ?([[:digit:]]+) ?\]/i";
   $t=preg_replace($regCouleur, "<span style=\"font-size: \\1px\">", $t);

   // lien
   $regLienSimple="/\[url\] ?([^\[]*) ?\[\/url\]/i";
   $regLienEtendu="/\[url ?=([^\[]*) ?] ?([^]]*) ?\[\/url\]/i";
   if (preg_match($regLienSimple, $t)) 
           $t=preg_replace($regLienSimple, '<a href="\\1">\\1</a>', $t);
   else $t=preg_replace($regLienEtendu, '<a href="\\1" target="_blank">\\2</a>', $t);

   return $t;
}
?>



