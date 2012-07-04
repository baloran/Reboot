<?php
class Manager{
                                                    // PARAMETRAGE DU PROJET (MANUEL)
    public static       $DN         = '';           // Nom de domaine du projet
    public static       $SD         = '';           // Sous domaine du projet
    public static       $FOLDER     = '';           // Sous-dossier par rapport au domaine

                                                    // PARAMETRAGE DU SERVER (AUTOMATIQUE)
    public static       $URI        = '';           // URI du projet sur le serveur
    public static       $SERVER     = '';           // Type de serveur
    public static       $HOME       = '';           // Pase d'accueil du projet

                                                    // PARAMETRAGE COURANT (AUTOMATIQUE)
    public static       $CMENU      = 'index';      // Menu courant
    public static       $CLNG       = 'fr';         // Langue courante
    public static       $Language   = array('fr');

    public static       $USER;                      // Utilisateur
    public static       $INCLUDE;                   // Include CSS/JS  
    public static       $MENU;                      // Menu
    private static      $Connexion;
    
    // CONTEXTE D'ERREUR
    // Dernière requête
    public static       $QUERY;
    // Dernier objet
    public static       $OBJECT;
    // Dernière fonction
    public static       $FUNCTION;
    // Message d'erreur
    public static       $MSG;

    // CONTEXTE DE TEST
    // Si true fait un echo de la prochaine requète
    public static       $BREAK_QUERY;
    public static       $debug = true;
    protected static    $statut = 2;
    /* -1: DESACTIF
     * 1 : OK
     * 2 : INSCRIPTION / GUEST DESAC
     * 3 : CONNEXION DESAC
     */

    public static       $isMobile = false;

    // Instaure les paramètres de connexion
    public static function setEngine($connexions){ 
        Manager::$Connexion = $connexions;
    }

    // Créer l'objet PDO
    public static function getEngine($db){
        return new PDO(
            'mysql:host='.Manager::$Connexion[$db]['hote'].';dbname='.Manager::$Connexion[$db]['db'], 
            self::$Connexion[$db]['user'], 
            self::$Connexion[$db]['password']);
    }

    public static function getStatut(){
        return self::$statut;
    }

    // 
    public static function start(){
        self::initIncludePath();

        $authName = 'auth/'.str_replace('/', '_', substr(self::$HOME, 0, -1));
        $authFile = self::$URI.'GLOBAL/'.$authName.'.php';

        // Recupère le fichier de configuration
        if(file_exists($authFile))
            self::addGlobalFile($authName);
        // S'il n'existe pas, le fichier est crée
        else{
            $fp = fopen($authFile, "a+");
            $authStr = "<?php
                Manager::setEngine(array(
                    'Home' => array(
                        'hote'      => 'localhost',
                        'user'      => 'emptyuser',
                        'password'  => 'passw0rd',
                        'db'        => 'reboot'
                    )
                ));
            ?>";
            
            fputs($fp, $authStr);
            fclose($fp);

            self::addGlobalFile($authName);
        }
        

        Manager::$USER = new User();
        $retour = self::$USER->importSession();

        if(!is_set($_SESSION, 'ERR')){
            $_SESSION['ERR'] = $retour;
        }
    }
    
    // Initialisation des différents paramètres
    public static function init() {
        self::initLanguage();
        self::initSession();
        self::initVariables();
    }
    
    // Initialisation des includes global
    public static function initInclude(){
        if($_SERVER['SERVER_ADDR'] == '192.168.0.1'){
            self::$SERVER = 'NAS';
            self::$URI = '/var/services/web/';
        }
        elseif($_SERVER['SERVER_ADDR'] == '127.0.0.1'){
            self::$SERVER = 'LOCALHOST';
            self::$URI = 'D:/CloudStation/Programmes/UwAmp/www/';
            self::$DN = 'localhost';
        }
        elseif($_SERVER['SERVER_ADDR'] == '176.31.156.90'){
            self::$SERVER = 'DEDIE';
            self::$URI = '/var/www/';
        }

        self::addGlobalFile('functions/autres');
        self::addGlobalFile('functions/format');
        self::addGlobalFile('functions/calcul');
        self::addGlobalFile('functions/builder');
        self::addGlobalFile('functions/is');
    }
    
    // Initalisation des includes propre au projet
    public static function initIncludePath(){
        $includePath = self::$SERVER != 'LOCALHOST' ? Manager::$DN : '';
        $home = Manager::$DN;

        if(Manager::$SD != ''){
             if(self::$SERVER != 'NAS')   $includePath .= '/'.Manager::$SD;

            $home = Manager::$SD.".$home";
        }
        if(Manager::$FOLDER != ''){
            $home .= '/'.Manager::$FOLDER;
            $includePath .= '/'.Manager::$FOLDER;
        } 

        Manager::$HOME = "$home/";

        set_include_path(Manager::$URI.$includePath);
    }
    
    // Verification du statut du projet
    public static function verificationStatut(){
        if(self::$statut == -1){
            self::toExit();
        }
        if(self::$statut == 0 && isset(self::$USER) && self::$USER->statut < 3){
            self::toMaj();
        }
        if($_SERVER['REQUEST_URI'] == '/noway' || ($_SERVER['REQUEST_URI'] == '/update' && self::$statut > 0)){
            header('refresh: 5;url='.Manager::$HOME);
        }
    }
    
    // Initalise le language
    public static function initLanguage(){
        setlocale(LC_CTYPE,"fr_FR.ISO-8859-1");
        date_default_timezone_set('Europe/Paris');
        
        if(isset($_GET['lng']) && in_array($_GET['lng'],Root::$Language)){
            self::$CLNG = $_GET['lng'];
            setcookie('lng', self::$CLNG, time()+3600*24*31,'/','.'.self::$DN);
            self::toBack();
        }
        elseif(isset($_COOKIE['lng']) && in_array($_COOKIE['lng'],self::$Language)){
            self::$CLNG = $_COOKIE['lng'];
        }
        elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
            //echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
    }

    // Initialise les paramètres d'erreur
    public static function initError(){
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 'On');
        ini_set('display_startup_errors', 'On');
    }

    // Initialise les paramètres de session
    public static function initSession(){
        ini_set('session_path', '/');
        ini_set('session_domain', '.'.self::$DN);
        ini_set('session_name', 'PHPSESSID');
        @session_set_cookie_params(0, '/', '.'.self::$DN, false);

        set_exception_handler('exception_handler');
        set_error_handler('error_handler');
    }
    
    // Inclue un fichier global
    public static function addGlobalFile($class){
        $uri = self::$URI.'GLOBAL/'.$class.'.php';
     
        if(file_exists($uri)) 
            include_once $uri;
        else{
            echo $ERROR = "File not found : $uri </pre>";
            exit;
        }
    }
    
    // Initialisation des variables du projet
    public static function initVariables(){
        self::$INCLUDE = array();
        self::$INCLUDE['js'] = array();
        self::$INCLUDE['css'] = array();
        self::$INCLUDE['style'] = array();
    }
    
    // Recupère le nom du fichier par rapport au dossier du projet
    public static function getFile($file){
        $pos = strpos($file, Root::$URI);

        if($pos !== false) {
            $file = substr($file, $pos + strlen(Root::$URI));
        }
        
        return $file;
    }
    
    // Initialise le tracage 
    public static function setTrace(){
        $trace = debug_backtrace(false);
        
        $tabTrace = '';
        $traces = debug_backtrace(false);
        for($i = 3; $i < count($traces) && $i <= 10; $i++){
            $strtrace = $file = '';
            $trace = $traces[$i];
        
            if(isset($trace['file'])){
                $file = Manager::getFile($trace['file']);
            }
            
            if(isset($trace['function']))   $strtrace .= $trace['function'];
            if($file != '')                 $strtrace .= ' on '.$file;
            if(isset($trace['line']))       $strtrace .= ' line '.$trace['line'];
            
            if($trace != '')                $tabTrace[] = $strtrace;
        }

        return $tabTrace;
    }
    
    // Crée un rapport d'erreur
    public static function initThrow(){
        $ERROR = array();
        $ERROR['trace'] = self::setTrace();
        
        if(self::$OBJECT && !self::$OBJECT->isOk){
            $ERROR['url']       = getCurrentUrl();
            $ERROR['request']   = self::$QUERY;
            $ERROR['class']     = self::$OBJECT->className;
        }
        
        if(isset($_SERVER['HTTP_REFERER'])){
            $ERROR['referer'] = $_SERVER['HTTP_REFERER'];
        }
        
        return $ERROR;
    }
    
    // Envoie rapport d'erreur
    public static function sendThrow($ERROR, $type){
        $Log = new Log();
        
        if(self::$debug){
            echo 'ERREUR_______'.$type.'_____________'.PHP_EOL;
            echo '<pre>';
            print_r($ERROR);

            exit;
        }
        else{
            $Log->sendError($ERROR, $type); 
            $_SESSION['ERR'] = 'ERROR';
            self::toBack();
        }
    }

    // Fonction test d'affichage des variables
    public static function testEnvironnement(){
        echo '<pre>';
        print_r(array(
            '_______PARAM PROJET______'=>'____________________________________',
            'DN'            => self::$DN,
            'SD'            => self::$SD,
            'FOLDER'        => self::$FOLDER,
            'HOME'          => self::$HOME,
            '_______PARAM SERVER______'=>'____________________________________',
            'URI'           => self::$URI,
            'SERVER'        => self::$SERVER,
            'INC'           => get_include_path(),
            '_______PARAM COURANT______'=>'____________________________________',
            'CurrentMenu'   => self::$CMENU,
            'CurrentUrl'    => getCurrentUrl(),
            'CurrentPage'   => getCurrentPage()
        ));
    }

    // Fonction test de tracage d'erreur
    public static function testTrace($other = false){
        $ERROR = self::initThrow();
        
        echo '<pre>';
        print_r($ERROR);
        if($other) print_r($other);
        
        exit;
    }
    
    // Erreur 404
    public static function toExit(){
        header('Location: '.build_url('noway'));
        exit;
    }
    
    // Retour arrière
    public static function toBack($gotohome = false){
        if(!isset($_SERVER['HTTP_REFERER']) || $gotohome) $_SERVER['HTTP_REFERER'] = getCurrentPage();
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit;
    }
    
    // Mise à jour du serveur en cours
    public static function toMaj(){
        if($_SERVER['REQUEST_URI'] != '/update'){
            header('Location: '.build_url('update'));
            exit;
        }
    }
}


// Chargement automatique de classe
function __autoload($class_name) {
    if(in_array($class_name,Root::$NotClasse) === false){
        $file = '';

        $TabUri = array(
            'LOCAL'     => get_include_path().'/model/php/'.$class_name.'.php',
            'GLOBAL'    => Manager::$URI.'GLOBAL/classes/'.$class_name.'.php'
        );

        foreach($TabUri as $type => $uri){
            if(file_exists($uri)){
                $file = $uri;
                break;
            }
        }

        if($file){
            include_once $file;
        }
        else{
            echo $ERROR = "Class '$class_name' not found : <pre>".print_r($TabUri, true)."</pre>";
            //self::$ROOT->sendThrow($ERROR, '404');
        }  
    }
}

session_start();
?>