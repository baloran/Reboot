<?php

Manager::$MENU['header'] = array(
	'home'			=>	'Accueil'
);
Manager::$MENU['footer'] = array(

);
Manager::$MENU['descriptions'] = array(
	'home'			=>	array('accueil', ''),
	'white_rabbit'	=>	array('identification', ''),
	'red_pill'		=>	array('subscription', ''),
	'play'			=>	array('jeu', '')
);

function build_header(){
	$strHead = '';

	if(Manager::$CMENU != 'white_rabbit'){

		if(Root::$inGame){
			if(!Manager::$USER->isConnected) {
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