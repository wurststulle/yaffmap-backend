<?php
class YaffmapResponse{
	
	const OPERATION_SUCCEDED = '0';
	const OPERATION_FAILED = '1';
	const NODEID_NOT_FOUND = '2';
	const NEW_AGENT_RELEASE_FOUND = '3';
	const NEW_AGENT_RELEASE_NOT_FOUND = '4';
	
	const DATA_SEPARATOR = ';';
	const RETURNSTRING_SEPARATOR = '|';
	
	private static $me = null;
	
	protected $responseCode = self::OPERATION_FAILED;
	protected $responseMsg = '';
	protected $data = '';
	protected $severity;
	
	/**
	 * @return YaffmapResponse
	 */
	public static function getInstance(){
		if(is_null(self::$me)){
			self::$me = new YaffmapResponse();
		}
		return self::$me;
	}
	
	public function setResponseCode($code){
		$this->responseCode = $code;
	}
	
	public function setResponseMsg($msg){
		$this->responseMsg = $msg;
	}
	
	public function appendResponseMsg($msg){
		$this->responseMsg .= $msg;
	}
	
	public function addResponseData($data){
		if($this->data == ''){
			$this->data = $data;
		}else{
			$this->data .= self::DATA_SEPARATOR.$data;
		}
	}
	
	public function setSeverity($severity){
		$this->severity = $severity;
	}
	
	public function reset(){
		$this->responseCode = self::OPERATION_FAILED;
		$this->responseMsg = '';
		$this->data = '';
	}
	
	public function __toString(){
		return $this->responseCode.self::RETURNSTRING_SEPARATOR.$this->responseMsg.self::RETURNSTRING_SEPARATOR.$this->data.self::RETURNSTRING_SEPARATOR.$this->severity;
	}
}
?>