<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/model/php/Root.php';

	$gotohome = false;

	$action = is_set($_GET, 'action') ? encode($_GET['action']) : null;
	$id 	= is_set($_GET, 'id') && is_numeric($_GET['id']) ? encode($_GET['id']) : 0;
	
	if(is_set($_POST, 'pseudo')) _VAR::$ROOT->toExit();
	if($action == 'identification' || $action == 'subscription'){
		if(!is_set($_POST, 'val1') || !is_set($_POST, 'val2')) _VAR::$ROOT->toBack();
	}
	elseif($action == 'update' || $action == 'unsubscribe'){
	    if(!_VAR::$USER->isConnected) _VAR::$ROOT->toExit();
	}

	switch($action){
		/************************************/
	    /*          IDENTIFICATION          */
	    /************************************/
		case 'identification':
            $cookie = is_set($_POST, 'cookie') && $_POST['cookie'] ? true : false;

            if(!_VAR::$USER->isConnected){
                $Verification = _VAR::$USER->identification(trim($_POST['val1']), trim($_POST['val2']), $cookie);
              
                if($Verification != 'OK'){
                     $_SESSION['ERR'] = $Verification;
            	}
            }
	    break;

	    /************************************/
	    /*          SUBSCRIPTION           */
	    /************************************/
	    case 'subscription':  
			$params = array(
		        'pseudo' 	=> $_POST['val1'],
		        'email' 	=> $_POST['val2']/*,
		        'cgu' 		=> $_POST['cgu']*/
		    );
		    $retour = User::verification_information($params);

		    if($retour != 'OK'){
		    	$_SESSION['pseudo'] = $params['pseudo'];
	        	$_SESSION['email'] 	= $params['email'];
	        	$_SESSION['ERR']	= $retour;
		    }
		    else{
		    	unset($_SESSION['pseudo']);
	        	unset($_SESSION['email']);
	            $retour = _VAR::$USER->subscription($params);

	            if($retour == 'OK'){
	            	
	            }
	            else $_SESSION['ERR'] = $retour;
		    }
	    break;

	    /************************************/
	    /*          UNSUBSCRIBE             */
	    /************************************/
	    case 'unsubscribe': 
	    	if(is_set($_POST, 'verification') && $_POST['verification']){
	    		$retour = _VAR::$USER->unsubscribe();
	    		
	    		if($retour !== 'OK') $_SESSION['ERR'] = $retour;
	    	}
	    break;

	    /************************************/
	    /*          DECONNECTION            */
	    /************************************/
	    case 'deconnection':
	        $gotohome = true;
	        _VAR::$USER->deconnection();

	        $_SESSION['hash'] = '';
	        session_unset();
	        session_destroy();
	        setcookie('hash','',0,'/','.'._VAR::$ROOT->NDD);
	    break;

	    default:
	    	$_SESSION['ERR'] = 'ERROR_USER_NOACTION'; //@ERROR_CODE
	    	_VAR::$ROOT->toExit();
	    break;
	}

	//print_r($_SESSION);exit;
	_VAR::$ROOT->toBack($gotohome);
?>