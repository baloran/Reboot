<?php
$uriManager = '../../GLOBAL/classes/Manager.php';
while(!file_exists($uriManager)) $uriManager = "../$uriManager";
include_once $uriManager;

Manager::initError();
Manager::initInclude();

// En fonction du type de serveur on charge les paramètres
if(Manager::$SERVER == 'NAS'){
    Manager::$DN = 'indexed.fr';
    Manager::$FOLDER = 'reboot';
}
elseif(Manager::$SERVER == 'LOCALHOST'){
    Manager::$FOLDER = 'reboot';
}
else{
    Manager::$DN = 'opworldlab.com';
    Manager::$SD = 'reboot';
} 
Manager::initIncludePath();

class Root extends Manager{
    public static $NotClasse = array();
    
    /* Configuration du PROJET */
    public static $Email = array(
        'inscription'   => 'subscribe@opworldlab.com',
        'contact'       => 'contact@opworldlab.com'
    );
    
    protected static $statut = 2;
    public static $inGame = false;
    
    public static function init() {
        
        parent::init();

        include 'include/php/builder.php';

        if(getCurrentPage() != build_url('white_rabbit')){
            Manager::$INCLUDE['css'][0]    = 'css/global';
            Manager::$INCLUDE['css'][1]    = 'css/theme';
        }
        else{
            //Manager::$MENU['header'] = array();
        }

        
    }
}
Root::init();
Manager::start();
?>