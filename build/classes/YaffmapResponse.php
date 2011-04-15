<?php
/**
 * @deprecated
 */
class ResponseCodeNode{
	
	const OPERATION_SUCCEDED = '0';
	const OPERATION_FAILED = '1';
	const NODEID_NOT_FOUND = '2';
}

/**
 * @deprecated
 */
class Response extends YaffmapResponse{
	
}

class YaffmapResponse{
	
	const OPERATION_SUCCEDED = '0';
	const OPERATION_FAILED = '1';
	const NODEID_NOT_FOUND = '2';
	
	const DATA_SEPARATOR = ';';
	const RETURNSTRING_SEPARATOR = '|';
	
	protected $responseCode = 0;
	protected $responseMsg = '';
	protected $data = '';
	
	public function setResponseCode($code){
		$this->responseCode = $code;
	}
	
	public function setResponseMsg($msg){
		$this->responseMsg = $msg;
	}
	
	public function addData($data){
		if($this->data == ''){
			$this->data = $data;
		}else{
			$this->data .= self::DATA_SEPARATOR.$data;
		}
	}
	
	public function reset(){
		$this->responseCode = 0;
		$this->responseMsg = '';
		$this->data = '';
	}
	
	public function __toString(){
		return $this->responseCode.self::RETURNSTRING_SEPARATOR.$this->responseMsg.self::RETURNSTRING_SEPARATOR.$this->data;
	}
	
	/**
	 * @deprecated
	 * @param unknown_type $msg
	 */
	public function setErrorMsg($msg){
		$this->responseMsg = $msg;
	}
	
	/**
	 * @deprecated
	 * @param unknown_type $code
	 */
	public function setErrorCode($code){
		$this->responseCode = $code;
	}
}
?>