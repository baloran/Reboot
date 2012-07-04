<?php

function getCurrentUrl(){
    return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

function getCurrentPage(){
    return 'http://'.Manager::$HOME.Manager::$CMENU;
}

function build_url($uri = ''){
	if(substr($uri, 0, 7) != 'http://')
		$uri = 'http://'.Manager::$HOME.$uri;
	return $uri;
}

function build_head(){
	$current = substr($_SERVER['REQUEST_URI'], 1);
	if(Manager::$FOLDER) $current = str_replace(Manager::$FOLDER.'/', '', $current);
	if(strpos($current, '?') > 0) $current = substr($current, 0, strpos($current, '?'));
	if(strpos($current, '#') > 0) $current = substr($current, 0, strpos($current, '#'));
	if($current == '') $current = 'home';
	Manager::$CMENU = $current;

	$page = isset(Manager::$MENU['descriptions'][$current]) ? $current : 'home';
	$details = Manager::$MENU['descriptions'][$page];

	$strHead = array();

	$strHead[] = '<title>'.display(ucfirst($details[0])).'</title>';
	$strHead[] = '<meta name="description" content="'.display($details[1]).'"/>';
	$strHead[] = '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15"/>';

	if(Manager::$isMobile){
		$strHead[] = '<meta name="viewport" content="width=device-width, user-scalable=no" />';
		if(file_exists(get_include_path()."/include/css/mobile/$page.css")) Manager::$INCLUDE['css'][] = 'css/mobile/'.$page.'.css';
	}
	elseif(file_exists(get_include_path()."/include/css/$page.css")){
		Manager::$INCLUDE['css'][] = 'css/'.$page.'.css';
	}
	
	ksort(Manager::$INCLUDE['css']);
	ksort(Manager::$INCLUDE['js']);

	foreach(Manager::$INCLUDE['css'] as $cssFile){
	    $strHead[] = '<link href="'.build_url($cssFile).'" rel="stylesheet" />';
	}
	foreach(Manager::$INCLUDE['js'] as $jsFile){
	    $strHead[] = '<script src="'.build_url($jsFile).'"></script>';
	}

	if(count(Manager::$INCLUDE['style']) > 0){
		$strHead[] = '<style>';
		foreach(Manager::$INCLUDE['style'] as $style){
		    $strHead[] = $style;
		}
		$strHead[] = '</style>';
	}

	return implode(PHP_EOL."\t", $strHead).PHP_EOL;
}

function build_menu($menu){
	$menu = Manager::$MENU[$menu];
	$strMenu = '';

	foreach($menu as $link => $label){
		$class = Manager::$VAR['PAGE'] == $link ? 'current' : '';

		$strMenu .= '<li class="'.$class.'"><a href="'.build_url($link).'">'.ucfirst($label).'</a></li>';
	}

	return $strMenu;
}

function build_error($error = null){
	$strError = '';
	$error = $_SESSION['ERR'];
    $_SESSION['ERR'] = '';

    if($error != ''){
        $strError = '<div id="Error" title="Informations" ><span onclick="$(\'#Error\').hide();">&#215;</span><p class="motifrayure1">'.nl2br(display($error)).'</p></div>';
    }

    return $strError;
}


?>