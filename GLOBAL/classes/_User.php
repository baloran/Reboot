<?php
class _User extends Object{
    public $id              = 0;
    public $pseudo          = 'Visiteur';
    public $statut          = 0;
    public $isConnected     = false;

    public static $Pattern = array(
        'pseudo'    => '^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$',
        'email'     => '^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$'
    );
	
    private $KeyParam = array(
        'Block'	=> array('id'),
        'Admin' => array('pseudo', 'statut', 'hash', 'uid')
    );

    public function __construct()
    {
        parent::__construct('User');
    }
	
    public function __get($info){
        $return = false; 
        $rubrique = 'user_'.$info;
        
        $this->SELECT[] = $rubrique;
        if($this->id) $this->WHERE = "user_id = '".encode($this->id)."'";
        else $this->WHERE = "user_pseudo = '".encode($this->pseudo)."'";
        
        $sqlId = $this->SELECT();
        if($sqlId && ($row = $sqlId->fetch())){
            $return = $row->$rubrique;
			
            if($info == 'id' || $info == 'pseudo') $this->$info = $row->$rubrique;
        }
        $sqlId->closeCursor();
        
        return $return;
    }
    
    public function __set($info, $value){
    
    }

    public function maj(&$tabInformations){
        $return = $this->verification_information($tabInformations);
        if($return !== 'OK') return $return;

        $keyImp     = array('id');
        $keyAdmin   = array('pseudo', 'statut', 'hash', 'uid');
        
        $this->VALUES = array();
        $this->setId();
        
        $return = $this->_update($tabInformations, true, $keyImp, $keyAdmin);

        return $return;
    }
    
    public static function _last($nb){
        $return = array();
        
        $user = new User();
        $user->SELECT[] = 'user_pseudo';
        $user->SELECT[] = 'user_dateCreate';
        $user->WHERE    = 'user_statut > 1';
        $user->last($nb);

        $reqListUsers = $user->SELECT();
        
        while($row = $reqListUsers->fetch()){
            $tabDate = explode(' ',date2fr($row->user_dateCreate));
            $row->user_dateCreate = $tabDate[0];
            $return[] = $row;
        }
        $reqListUsers->closeCursor();
        return $return;
    }

    /**
     * Identification de l'utilisateur
     * @param string $pseudo
     * @param string $password
     * @param bool $cookie
     * @return string 
     */
    public function identification($pseudo, $password, $cookie = false){
        setcookie('hash','',0,'/','.'.Manager::$DN);
        $return         = 'ERROR_USER_IDENTIFICATION'; //@ERROR_CODE
     
        // Verification de l'utilisateur
        $return = $this->verification('identification', 
            array(
                'pseudo' => $pseudo, 
                'password' => $password
            )
        );

        // Si tout est OK, on initialise sa session
        if($return == 'OK'){
            $wDate = date("Ymd");
            $this->setSession($cookie);

            $Log = new Log();
            $Log->sendStat('connection'); 
        }
        else $this->clearSession();

        return $return;
    }

    public function verification_moderation($params = array()){
        $return = 'OK'; //@ERROR_CODE

        $verification = new Object('Moderation');
        $verification->SELECT[] =   'moderation_id';

        if(!is_set($params, 'id')) $params['id'] = $this->id;
        if(!is_set($params, 'ip')) $params['ip'] = $_SERVER['REMOTE_ADDR'];

        $verification->WHERE .= "(moderation_user_id = '".encode($params['id'])."'
            OR moderation_ip = '".encode($params['ip'])."')";

        $req = $verification->SELECT();
        if($row = $req->fetch()) $return = 'USER_BLOCK'; //@ERROR_CODE
        $req->closeCursor();

        return $return;
    }

    public static function verification_information(&$params){
        if(isset($params['pseudo'])){
            $params['pseudo'] = trim($params['pseudo']);
            if($params['pseudo'] == '')                                 return 'USER_NO_PSEUDO'; //@ERROR_CODE
            if(preg_match("/".User::$Pattern['pseudo']."/i", $params['pseudo']) === false)      
                                                                        return 'USER_ER_PSEUDO'; //@ERROR_CODE
        }

        if(isset($params['password'])){
            $params['password'] = trim($params['password']);
            if($params['password'] == '')                               return 'USER_NO_PASSWORD'; //@ERROR_CODE
        }     
        if(isset($params['email'])){
            $params['email'] = trim($params['email']);
            if($params['email'] == '')                                  return 'USER_NO_EMAIL'; //@ERROR_CODE
            if(is_email($params['email']) == false)                     return 'USER_ER_EMAIL'; //@ERROR_CODE
        }  
        if(isset($params['dateNaissance'])){
            if(!is_set($params, 'dateNaissance') || 
                !is_datetime($params['dateNaissance']))                 return 'EVENT_ER_DATENAISSANCE'; //@ERROR_CODE
        }
        if(isset($params['cgu'])){
            $params['user_cgu'] = trim($params['user_cgu']);
            if($params['user_cgu'] == 'false' || $params['user_cgu'] == '' || $params['user_cgu'] == '0' || !$params['user_cgu'])   
                                                                        return 'USER_ER_CGU'; //@ERROR_CODE
        }  
        if(isset($params['sexe'])){
            if(in_array($params['sexe'], array(0,1,2)) === false){
                $params['sexe'] = 0;
            }
        }



        return 'OK';
    }
       
    /**
     * Verification de l'utilisateur (Existence, Inscription)
     * Params contient soit le hash pour une import session, soit le login/mdp pour une identification
     * Retourne un mot clef
     * @param string $action
     * @param array $Params
     * @return string 
     */
    public function verification($action, $params = array()){
        $return         = 'ERROR_USER_VERIFICATION'; //@ERROR_CODE
    
        $this->SELECT[] =   'user_id';
        $this->SELECT[] =   'user_pseudo';
        $this->SELECT[] =   'user_email';
        $this->SELECT[] =   'user_statut';
        $this->SELECT[] =   'moderation_user_id';

        $this->FROM['Moderation'] = array('LEFT JOIN', 'user_id = moderation_user_id');
        $this->WHERE =      "moderation_ip =  '".encode($_SERVER['REMOTE_ADDR'])."' OR ";

        if($action == 'identification' || $action == 'exist'){
            $retour = self::verification_information($params);
            if($retour != 'OK') return $retour;
        }

        if($action == 'identification'){
            $this->WHERE .= "(user_pseudo = '".encode($params['pseudo'])."'
                OR user_email = '".encode($params['pseudo'])."')
                AND user_password = '".format_password(encode($params['password']))."'";
        }
        elseif($action == 'session'){
            $this->WHERE .= "(user_hash = '".encode($params['hash'])."')";
        }
        elseif($action == 'activation'){
            $this->WHERE .= "(user_statut > 1 AND user_uid = '".encode($params['uid'])."')";
        }
        elseif($action == 'exist'){
            $this->WHERE .= "(user_pseudo = '".encode($params['pseudo'])."'
                OR user_email = '".encode($params['email'])."')";
        }
        else return "ERROR_USER_NOACTION"; //@ERROR_CODE

        $req = $this->SELECT();
        if($row = $req->fetch()){
            if($row->moderation_user_id){
                $return = 'USER_BLOCK'; //@ERROR_CODE
            }
            elseif($row->user_statut == 1 && ($action == 'identification' || $action == 'session')){
                $return = 'USER_INACTIVE'; //@ERROR_CODE
            }
            elseif(Manager::getStatut() == 3 && $row->user_statut < 3){
                return 'SERVICE_DESAC'; //@ERROR_CODE
            }
            else{
                if($action == 'identification' || $action == 'session' || $action == 'activation'){
                    $this->id = $row->user_id;
                    $this->pseudo = $row->user_pseudo;
                    $this->statut = $row->user_statut;
                    $this->isConnected = true;
                    $return = 'OK';
                }
                elseif($action == 'exist'){
                    if($row->user_pseudo == encode($params['pseudo'])) $return = 'PSEUDO_EXIST'; //@ERROR_CODE
                    elseif($row->user_email == encode($params['email'])) $return = 'EMAIL_EXIST'; //@ERROR_CODE
                } 
                else $return = 'ERROR_NOACTION';
            }
        }
        else{
            if($action == 'identification' || $action == 'activation') $return = 'USER_WRONG'; //@ERROR_CODE
            elseif($action == 'session') $return = 'USER_DECONECT'; //@ERROR_CODE
            elseif($action == 'exist') $return = 'USER_NOEXIST';
        }
        $req->closeCursor();

        return $return;
    }
	
    /**
     * Import la session de l'utilisateur courant
     * Met à jour sa dernière connexion
     * @return string 
     */
    public function importSession(){
        $authorized     = false;
        $cookie         = false;
        $user_hash      = '';
       
        if(is_set($_SESSION, 'hash')){
            $user_hash      = $_SESSION['hash'];
            if(isset($_COOKIE['hash']) && $_COOKIE['hash'] == $user_hash){
                $cookie         = true;
            }
        }
        elseif(is_set($_COOKIE, 'hash')){
            $user_hash      = $_COOKIE['hash'];
            $cookie         = true;
        }

        // Verification de l'utilisateur
        if(is_set($user_hash)){
            $return = $this->verification('session', 
                array(
                    'hash' => $user_hash
                )
            );
        }
        else $return = $this->verification_moderation();

        // Si tout est ok
        if($this->isConnected){
            // Mise à jour de la session et de la derière connexion
            $this->setSession($cookie);
        }
        else $this->clearSession();
        
        if($return == 'OK') $return = ''; //@ERROR_CODE
        elseif($return == 'USER_BLOCK'){ //@ERROR_CODE
            Manager::toExit();
        }

        return $return;
    }

    public function clearSession(){
        if(isset($_SESSION['hash'])) {
            $_SESSION['hash'] = '';
            unset($_SESSION['hash']);
        }
        if(isset($_COOKIE['hash'])) {
            $_COOKIE['hash'] = '';
            unset($_COOKIE['hash']);
        }
    }
	
	/**
     * Mise en place de la session
     * @param bool $cookie 
     */
    public function setSession($cookie){
         // Mise à jour de la dernière connexion
        $this->VALUES = array();
        $this->WHERE = '';
        $this->setDate(false);
        $this->setId($this->id);
        
        $sqlUpdate = $this->UPDATE();

        $user_hash = md5(uniqid(rand(),TRUE));
        $this->VALUES = array();
        $this->VALUES['user_hash'] = $user_hash;
        $this->WHERE = '';
        $this->setId($this->id);

        $sqlUpdate = $this->UPDATE();
        
        if($sqlUpdate && $sqlUpdate['nombre'] > 0){
            $_SESSION['hash'] = $user_hash;
            if($cookie){
                setcookie('hash',$user_hash,time()+3600*24*31,'/','.'.Manager::$DN);
            }
        }
    }
    
    /**
     * Deconnexion de l'utilisateur
     */
    public function deconnection(){
        $this->VALUES = array();
        $this->VALUES['user_hash'] = "";
        $this->WHERE = '';
        $this->setId($this->id);
        
        $this->UPDATE();
    }
	
	public function sendMail($object, $body, $email = false){
        if(!$email){
            $email = $this->getInfo('email');
        }
        
        sendMail($email, $object, $body);
    }

    /**
     * Enregistrement de l'utilisateur
     * @param string $pseudo 
     * @param string $pseudo 
     */
    public function subscription($params){
        if(Manager::getStatut() != 1)   return 'SUBSCRIPTION_DESAC'; //@ERROR_CODE
        if($this->isConnected)              return 'USER_CONNECTED'; //@ERROR_CODE

        $return = $this->verification('exist', $params);
        
        if($return == 'USER_NOEXIST'){
            $password = randomPasword(8);
            $uid = base64_encode(time());

            $this->VALUES = array();
            $this->setDate(true);
            $this->VALUES['user_pseudo'] = $params['user_pseudo'];
            $this->VALUES['user_email'] = $params['user_email'];
            $this->VALUES['user_uid'] = $uid;
            $this->VALUES['user_password'] = $password;
            $this->WHERE = '';
            
            $reqSubscription = $this->INSERT();
            if($reqSubscription && $reqSubscription['nombre'] > 0){
                $this->id = $reqSubscription['dernier_id'];
                $return = 'OK';
            }
            else $return = 'ERROR_USER_SUBSCRIPTION'; //@ERROR_CODE
        }

        return $return;
    }

    public function validation($uid){

        $return = $this->verification('activation', array('uid' => $uid));

        if($return == 'OK'){
            $new_uid = base64_encode(time());
            
            $this->VALUES = array();
            $this->VALUES['user_uid'] = $new_uid;
            $this->VALUES['user_statut'] = 2;
            $this->WHERE = "user_uid = '".encode($uid)."'";
            
            $reqValidation = $this->UPDATE();
            
            if($reqValidation['nombre'] == 0) $return = 'USER_NOVALIDATION'; //@ERROR_CODE
        }
        
        return $return;
    }

    /**
     * Désinscription de l'utilisateur
     */
    public function unsubscribe(){
        if(!$this->isConnected) return 'USER_DECONECT'; //@ERROR_CODE

        $this->setId($this->id);
        $this->WHERE = '';

        $deleteUser = $this->DELETE();
        if($deleteUser['nombre'] > 0){
            $return = 'OK';
        }
        else $return = 'ERROR_ERROR_UNSUBSCRIBE'; //@ERROR_CODE
        
        return $return;
    }

    /*******************************************************************/
    /*                      PLUS                                       */
    /*******************************************************************/
    
    public static function count($Object = false){
        $return = false;
        $Object = $Object ? $Object : new User();

        $Object->SELECT = array();
        $Object->SELECT[] = 'count(user_id) as nb';
        if($Object->WHERE != '') $Object->WHERE .= ' AND ';
        $Object->WHERE .= "user_statut > 1";

        $sqlNb = $Object->SELECT();
        if($row = $sqlNb->fetch()){
            $return = $row->nb;
        }
        $sqlNb->closeCursor();

        return $return;
    }

    public static function connectedCount(){
        $Object = new User();

        $Object->WHERE .= 'user_dateUpdate >= DATE_ADD( NOW(), interval - 10 SECOND)';
        return User::count($Object);
    }

    public static function pseudo_similar($pseudo){
        $return = array();

        $this->SELECT = array();
        $this->SELECT[] = 'user_pseudo';
        $this->WHERE = "user_statut > 1
            AND user_pseudo SOUNDS LIKE '".encode($pseudo)."' LIMIT 10";
        
        $result = $this->SELECT();
        while($result && ($row = $result->fetch())){
            $return[] = $row->user_pseudo;
        }
        $result->closeCursor();
        
        return $return;
    }
}
?>