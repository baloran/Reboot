<?php

_VAR::$MENU['header'] = array(
	'home'			=>	'Accueil'
);
_VAR::$MENU['footer'] = array(

);
_VAR::$MENU['descriptions'] = array(
	'home'			=>	array('accueil', ''),
	'white_rabbit'	=>	array('identification', ''),
	'play'			=>	array('jeu', '')
);

function build_header(){
	$strHead = '';

	if(_VAR::$ROOT->CMENU != 'white_rabbit'){

		if(_VAR::$ROOT->inGame){
			if(!_VAR::$USER->isConnected) {
				header('Location:'.build_url('white_rabbit'));
				exit;
			}

			$strTitrePrincipal = 'OpWorldLab - Jeu';
			$strMenuPrincipal = '';
		}
		else{
			$strTitrePrincipal = 'OpWorldLab - News';
			$strMenuPrincipal = '';
		}

		$strHead = '<div id="TitrePrincipal">'.$strTitrePrincipal.'</div><ul id="MenuPrincipal">'.$strMenuPrincipal.'</ul>';

	}

	return $strHead;
}
?>