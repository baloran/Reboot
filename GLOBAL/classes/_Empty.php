<?
class Empty extends Object{

	public $id;
	
	public function __construct(){
		parent::__construct('Message');
	}

	public function verification_information(&$params){
		if(isset($params['title'])){
			$params['title'] = trim($params['title']);
			if($params['title'] == '')						return 'EVENT_NO_TITLE'; //@ERROR_CODE
		}

		return 'OK';
	}

	public function create($user, &$post){
		$return = $this->verification_information($post);
		if($return !== 'OK') return $return;	

		$this->setDate(true);

		$return = $this->_create($post, true);

		return $return;
	}


	public function maj($user, &$post){
		$return = $this->verification_information($post);
		if($return !== 'OK') return $return;

		$keyImp     = array('id');
        $keyAdmin   = array('user_id');

		$this->setDate(false);
		$this->setId();
		$return = $this->_update($post, true, $keyImp, $keyAdmin);

		return $return;
	}
}
?>