<?php
class KoboldResponseJson{

	public $value;
	public $action;
	public $error;
	
	public function __construct(){
		$this->action = array();
		$this->error = array();
	}
	
	public function addAction($action){
		$this->action[] = $action;
	}
	
	public function addError(KoboldResponseError $e){
		$this->error[] = $e;
	}
	
	public function script($response){
		$this->addAction($response);
	}
	
	public function alert($var){
		$this->addAction('alert(\''.$var.'\')');
	}
	
	public function redirect($url){
		$this->addAction('window.location="'.$url.'"');
	}
	
	public function setReturnValue($value){
		$this->value = $value;
	}
	
	public function get(){
		return json_encode($this);
	}
}

/**
 * basic error class for KoboldResponseJson
 */
class KoboldResponseError{
	
	public $errorType;
	public $message;
	
	public function __construct($errorType, $message = null){
		$this->errorType = $errorType;
		$this->message = $message;
	}
	
	public function setMessage($message){
		$this->message = $message;
	}
}

/**
 * @deprecated
 */
class KoboldResponseErrorPDO extends KoboldResponseError{
	
	public $file;
	public $line;
	
	public function __construct(Exception $e){
		$errorMessage = addslashes($e->getMessage());
		$errorMessage = str_replace('{', '\{', $errorMessage);
		$errorMessage = str_replace('}', '\}', $errorMessage);
		$this->file = $e->getFile();
		$this->line = $e->getLine();
		call_user_func_array('parent::__construct', array('pdo', $errorMessage));
	}
}
?>