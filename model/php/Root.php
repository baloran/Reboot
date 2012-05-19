<?
include_once $_SERVER['DOCUMENT_ROOT'].'/../GLOBAL/Manager.php';        

class Root extends Manager{
    public static $NotClasse = array();
    
    /* Configuration du PROJET */
    public static $Email = array(
        'inscription'   => 'subscribe@opworldlab.com',
        'contact'       => 'contact@opworldlab.com'
    );
    
	public static $HOME = 'beta.opworldlab.com';
    public static $FOLDER = '';
    protected $statut = 1;

    public $inGame = false;
	
    public function __construct() {
        set_include_path(Manager::$HOME.Root::$HOME);
        
        $this->NDD = 'beta.opworldlab.com';
        _VAR::$USER = new User();
        
        parent::__construct();

        include 'include/php/builder.php';
        
        if($this->CURL != $this->URL.'white_rabbit'){
            _VAR::$INCLUDE['css'][0]    = 'css/global';
            _VAR::$INCLUDE['css'][1]    = 'css/theme';
        }
        else{
            //_VAR::$MENU['header'] = array();
        }

    }
}
Manager::start();
?>