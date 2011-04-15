<?php
class YaffmapGetConfig extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
//		$allowed = array();
//		$this->checkInput($allowed);
	}
	
	public function getConfig(){
		
		return $this->response;
	}
}
?>