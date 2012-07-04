<?php
/**
 * Description of _ENGINE
 *
 * @author JA
 */

class Object{
    
    public $className = '';
    private $primaryKey = '';
    private $typeQuery = 'SELECT';
    public  $distinct = false;
    
    public $SELECT = array();
    public $FROM = array();
    public $WHERE = '';
    public $HAVING = '';
    public $ORDER = array();
    public $VALUES = array();
    public $LIMIT = '';
    public $RESULT;
    
    protected $FIELDS = array();
    
    private $Engine;
    
    public $isOk = true;
    public $id = false;
    
    public function __construct($pClassName, $db = 'Home'){
        $this->className = $pClassName;
        $this->FROM[] = $pClassName;
        
        $this->Engine = Manager::getEngine($db);
        
        $this->Engine->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
        
        $reqDescribe = $this->Engine->query("SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, COLUMN_COMMENT, COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$this->className."'");
        
        while($reqDescribe && $rowField = $reqDescribe->fetch()){
            $this->FIELDS[$rowField[0]] = array(
                'type'      =>  $rowField[1],
                'length'    =>  $rowField[2],
                'comment'   =>  $rowField[3]
            );
            
            if($rowField[4] == 'PRI'){
                $this->primaryKey = $rowField[0];
            }
        }
        $reqDescribe->closeCursor();
    }
    
    public function setType($pType){
        $this->typeQuery = strtoupper($pType);
    }
    
    /*
     * lancée lorsque l'on invoque des méthodes inaccessibles dans le contexte de l'objet.
     */
    public function __call($method, $arguments){
        $this->setType($method);
        return $this->query($this->serialize());
    }
	
    public function query($query, $mode = PDO::FETCH_OBJ){
        $this->isOk = false;
        Manager::$QUERY = $query;
        Manager::$OBJECT = $this;
        
        $type = strtoupper(substr($query, 0, 6));

        if(Manager::$BREAK_QUERY){
            echo $query;
            if(Manager::$debug) exit;
            else Manager::$BREAK_QUERY = false;
        }
 
        try{
            if($type == 'SELECT'){
                $this->RESULT = $this->Engine->query($query);
                $this->RESULT->setFetchMode($mode);
            }
            else{
                $this->RESULT = Array( 'nombre' =>  $this->Engine->exec($query), 'dernier_id' => $this->Engine->lastInsertId());
            }
            $this->isOk = true;
        }
        catch(PDOException $e){
            $ERROR = Manager::initThrow();
            $ERROR['str'] = $e->getMessage();
            Manager::sendThrow($ERROR, 'SQL');
        }
        
        return $this->RESULT;
    }
    
    /*
     * détermine comment l'objet doit réagir lorsqu'il est traité comme une chaîne de caractères.
     */
    public function __toString() {
        return '<pre>'.print_r(array(
        'SELECT'	=>	$this->SELECT,
        'FROM'		=>	$this->FROM,
        'WHERE'		=>	$this->WHERE,
        'HAVING'	=>	$this->HAVING,
        'ORDER'		=>	$this->ORDER,
        'VALUES'	=>	$this->VALUES,
        'LIMIT'         =>      $this->LIMIT,
        //'FIELDS'        =>      $this->FIELDS,
        'REQUEST'	=> 	$this->serialize(),
        'RESULT'        =>      $this->RESULT
        ), true);
    }
    
    /*
     * Redige la requète SQL
     */
    public function serialize()
    {
        $sSql = '';
        switch($this->typeQuery){
            case	'SELECT':
                $sSql = 	$this->get('Select');
                $sSql .= 	$this->get('From');
                $sSql .= 	$this->get('Where');
                $sSql .= 	$this->get('Having');
                $sSql .= 	$this->get('Order');
				$sSql .= 	$this->get('Limit');
            break;

            case	'UPDATE':
                $sSql = 	'UPDATE '.$this->get('Table');
                $sSql .= 	$this->get('Set');
                $sSql .= 	$this->get('Where');
            break;

            case	'INSERT':
                $sSql = 	'INSERT '.$this->get('Into');
                $sSql .= 	$this->get('Values');
            break;

            case	'DELETE':
                $sSql = 	'DELETE FROM '.$this->get('Table');
                $sSql .= 	$this->get('Where');
            break;
        }
        return $sSql;
    }
    
    // Rédige un morceau de la requète SQL
    public function get($type){
        $sSql = '';
        switch(strtoupper($type)){
            case	'SELECT':
                $sSql	= 'SELECT ';
                if($this->distinct) $sSql .= 'DISTINCT ';
                $tSelect    = array();

                if(count($this->SELECT) > 0){
                    $sSql .= implode(', ',array_unique($this->SELECT));
                }
                else {
                    $sSql	.= '*';
                }
            break;

            case	'FROM':
                $sSql	= ' FROM ';
                $tFrom = array();
                $iFrom = 0;

                foreach($this->FROM as $key => $value){
                        if(gettype($key) == 'integer'){
                                $tFrom[$iFrom] = $value;
                                $iFrom++;
                        }
                        elseif(gettype($key) == 'string'){
                                $tFrom[$iFrom - 1] .= ' '.$value[0].' '.$key.' ON '.$value[1];
                        }
                }

                $sSql .= implode(', ', $tFrom);
            break;

            case	'WHERE':
                if(trim($this->WHERE) != ''){ 
                    $sSql	= ' WHERE '.$this->WHERE;
                }
            break;

            case	'HAVING':
                if(trim($this->HAVING) != ''){ 
                    $sSql	= ' WHERE '.$this->HAVING;
                }
            break;
            
            case	'LIMIT':
                if(trim($this->LIMIT) != ''){ 
                    $sSql	= ' LIMIT '.$this->LIMIT;
                }
            break;

            case	'ORDER':
                if(count($this->ORDER) > 0){
                    $sSql	= ' ORDER BY ';
                    $tOrder = array();

                    foreach($this->ORDER as $key => $order){
                        if(is_integer($key)) $tOrder[] = $order;
                        else $tOrder[] = $key.' '.$order;
                    }
                    $sSql .= implode(', ',$tOrder);
                }
            break;

            case	'SET':
                $sSql = ' SET ';
                $tSet = array();

                foreach($this->VALUES as $key => $value){
                    if($this->verifKey($key)){
                        $tSet[] = $key.' = '.$this->verifValue($key, $value);;
                    }
                }
                $sSql .= implode(', ',$tSet);
            break;

            case 	'INTO':
                $sSql	= 'INTO '.$this->FROM[0].' (';
                $tInto = array();

                foreach($this->VALUES as $key => $value){
                    if($this->verifKey($key)){
                        $tInto[] = $key;
                    }
                }
                $sSql .= implode(', ',$tInto);
                $sSql .= ')';
            break;

            case 	'VALUES':
                $sSql	= ' VALUES (';
                $tValues = array();

                foreach($this->VALUES as $key => $value){
                    if($this->verifKey($key)){
                        $tValues[] = $this->verifValue($key, $value);
                    }
                }
                
                $sSql .= implode(', ',$tValues);
                $sSql .= ')';
            break;

            case	'TABLE':
                $sSql	= $this->FROM[0];
            break;
        
            default:
                echo 'ERROR';
            break;
        }
        return $sSql;
    }
    
    // Verifie si la clef existe dans la table
    public function verifKey($key){
        return in_array($key, array_keys($this->FIELDS)) !== false;
    }
    
    // Verifie la cohérance de la valeur et s'occupe de l'encoder
    public function verifValue($key, $value){
        $params = $this->FIELDS[$key];
        $sValue = $value;
        
        if(strpos($params['type'], 'int') !== false){
            $sValue = intval($sValue, 10);
        }
        if(strpos($params['type'], 'decimal') !== false){
            $sValue = floatval($sValue);
        }
        
        if($this->isTypeStr($params['type'])){
            switch($params['comment']){
                case 'pseudo':
                    preg_match_all("/[\w-_àáâãäåçèéêëìíîïðòóôõöùúûüýÿ]+/i", $value, $matches);
                    $sValue = implode('',$matches[0]);
                break;

                case 'password':
                    $sValue = format_password($value);
                break;

                case 'email':
                    preg_match_all("/[\w-_@\.]+/i", $value, $matches);
                    $sValue = implode('',$matches[0]);
                break;

                case 'alphanum':
                    preg_match_all("/[\w]+/i", $value, $matches);
                    $sValue = implode('',$matches[0]);
                break;

                case 'file':

                break;
            }
        }
        
        $sValue = encode($sValue);
        
        if($this->isTypeStr($params['type'])){
            $sValue = "'".$sValue."'";
        }
        
        return $sValue;
    }

    private function isTypeStr($type){
        return  strpos($type, 'char') !== false || 
                strpos($type, 'date') !== false || 
                strpos($type, 'blob') !== false;
    }

    public function last($nb){
        $this->ORDER[$this->primaryKey] = 'DESC';
        $this->LIMIT = $nb;
    }

    public function getId($id){
        if($id !== false && $id !== '') return $this->id = $id;
        if($this->id !== false && $this->id !== '') return $this->id;
        if(isset($this->VALUES[$this->primaryKey])) return $this->id = $this->VALUES[$this->primaryKey];
        return false;
    }
    
    public function setId($id = false){
        $id = $this->getId($id);
        
        if($this->WHERE != '') $this->WHERE .= ' AND ';
        if($id !== false && $id !== '')
            $this->WHERE .= $this->primaryKey.' = '.encode($id);
        else
            $this->WHERE .= '0 = 1';
    }
    
    public function setUser($user){
        if($this->WHERE != '') $this->WHERE .= ' AND ';
        $this->WHERE .= strtolower($this->className).'_user_id = '.encode($user->id);
    }
    
    public function _get($key, $id = false){
        $return = null;
        if($key == 'id') $key = $this->primaryKey;
        
        if($key){
            if($id || $this->WHERE == ''){
                $this->setId($id);
            }
            $this->SELECT = array($key);

            $req = $this->SELECT();
            if($row = $req->fetch()){
                $return = $row->$key;
            }
        }
        return $return;
    }

    public function _create(&$post, $verifUser = false, $keyInterdit = false, $keyAdmin = false){
        return $this->_maj($post, $verifUser, $keyInterdit, $keyAdmin, 'CREATE');
    }

    public function _update(&$post, $verifUser = false, $keyInterdit = false, $keyAdmin = false){
        return $this->_maj($post, $verifUser, $keyInterdit, $keyAdmin, 'UPDATE');
    }

    public function _maj(&$post, $verifUser = false, $keyInterdit = false, $keyAdmin = false, $action){
        if($verifUser && !Manager::$USER->isConnected)    return 'USER_DECONECT'; //@ERROR_CODE

        $prefixe = strtolower($this->className);
        $info = $post;
        $return = false;

        foreach($info as $_key => $value){
            $key = $prefixe.'_'.$_key;

            if(in_array($key, array_keys($this->FIELDS)) !== false){

                if($keyInterdit && in_array($_key, $keyInterdit) !== false) break;
                if($keyAdmin    && in_array($_key, $keyAdmin) !== false && Manager::$USER->statut < 3) break;

                $this->VALUES[$key] = $value;

                unset($post[$_key]);
            }
        }

        if(count($this->VALUES) > 0){
            echo $this->serialize();
            exit;
            //$return = $this->$action();
            //if($return['nombre'] > 0) $return = true;
        }
    }

    public function _delete($id = false){
        return $this->getObjectById($id) ? $this->DELETE() : false;
    }
    
    public function getObjectById($id, $all = false){
        $return = false;
        $id = $this->getId($id);
       
        if($id){
            $this->SELECT = array();
            if(!$all) $this->SELECT[] = $this->primaryKey;
            $this->setId($id);
            
            $reqObject = $this->Select();
            
            if($reqObject){
                $return = $reqObject->fetch();
            }
        }
        
        return $return;
    }
    
    public function setDate($isInsert){
        $wDate = date("Y-m-d H:i:s");
        
        $this->VALUES[strtolower($this->className).'_dateUpdate'] = $wDate;

        if($isInsert) $this->VALUES[strtolower($this->className).'_dateCreate'] = $wDate;
    }  
    
    public function similar($key, $value){
        preg_replace('[^a-zA-Z]', '', $value);
        $value = format_stripAccents($value);

        if($this->WHERE != '') $this->WHERE .= ' AND ';
        $this->WHERE .= $key." SOUNDS LIKE '".encode($value)."'";
    } 
}

?>
