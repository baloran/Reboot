<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/model/php/Root.php';

	$gotohome = false;

	$action = is_set($_GET, 'action') ? encode($_GET['action']) : null;

	switch($action){
		/************************************/
	    /*                                  */
	    /************************************/
		case '':

	    break;

	    default:
	    	$_SESSION['MSG'] = 'ERROR_NOACTION'; //@ERROR_CODE
	    	_VAR::$ROOT->toExit();
	    break;
	}

	_VAR::$ROOT->toBack($gotohome);
?>