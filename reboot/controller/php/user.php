<?php
	include_once '../../model/php/Root.php';

	$gotohome = false;

	$action = is_set($_GET, 'action') ? encode($_GET['action']) : null;
	$id 	= is_set($_GET, 'id') && is_numeric($_GET['id']) ? encode($_GET['id']) : 0;
	
	if(is_set($_POST, 'pseudo')) Manager::toExit();
	if($action == 'identification' || $action == 'subscription'){
		if(!is_set($_POST, 'val1') || !is_set($_POST, 'val2')) Manager::toBack();
	}
	elseif($action == 'update' || $action == 'unsubscribe'){
	    if(!Manager::$USER->isConnected) Manager::toExit();
	}

	switch($action){
		/************************************/
	    /*          IDENTIFICATION          */
	    /************************************/
		case 'identification':
            $cookie = is_set($_POST, 'cookie') && $_POST['cookie'] ? true : false;

            if(!Manager::$USER->isConnected){
                $Verification = Manager::$USER->identification(trim($_POST['val1']), trim($_POST['val2']), $cookie);
              
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
	            $retour = Manager::$USER->subscription($params);

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
	    		$retour = Manager::$USER->unsubscribe();
	    		
	    		if($retour !== 'OK') $_SESSION['ERR'] = $retour;
	    	}
	    break;

	    /************************************/
	    /*          DECONNECTION            */
	    /************************************/
	    case 'deconnection':
	        $gotohome = true;
	        Manager::$USER->deconnection();

	        $_SESSION['hash'] = '';
	        session_unset();
	        session_destroy();
	        setcookie('hash','',0,'/','.'.Manager::$DN);
	    break;

	    default:
	    	$_SESSION['ERR'] = 'ERROR_USER_NOACTION'; //@ERROR_CODE
	    	Manager::toExit();
	    break;
	}

	//print_r($_SESSION);exit;
	Manager::toBack($gotohome);
?>