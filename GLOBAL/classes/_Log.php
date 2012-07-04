<?php
/**
 * Classe de gestion des Logs
 *
 * @author UrielMyeline
 */
class _Log extends Object{
    
    public function __construct()
    {
        parent::__construct('Log');
        
        if(isset(Manager::$USER) && Manager::$USER->isConnected){
            $this->VALUES['log_user_id'] = Manager::$USER->id;
        } 
        $this->VALUES['log_request'] = Manager::$QUERY;
        $this->VALUES['log_dateCreate'] = date("Y-m-d H:i:s");
        
        $Informations = informationsVisiteur();
        foreach($Informations as $key => $value){
            $this->VALUES['log_'.$key] = $value;
        }
    }
    
    public function sendError($error, $type){
        if($type == 'SQL'){
            $resume = $error['str'];
        }
        else{
            $file = str_replace(Manager::$HOME.'/','',$error['file']);
            $resume = $error['str'].' in '.$file.' line '.$error['line'];
            unset($error['context']);
        }
        
        $this->VALUES['log_type'] = "error ".$type;
        $this->VALUES['log_description'] = $resume;
        $this->VALUES['log_trace'] = serialize($error);
        
        $this->send();
    }
    
    public function sendLog($description){
        $this->VALUES['log_type'] = "log";
        $this->VALUES['log_description'] = $description;
    }
    
    public function sendStat($type){
        $this->VALUES['log_type'] = $type;
        $this->send();
    }
    
    public function send(){
        $this->INSERT();
    }
    
}
?>