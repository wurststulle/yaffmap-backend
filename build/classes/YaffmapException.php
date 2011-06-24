<?php
class YaffmapException extends Exception{
	
	public function __construct($message = null, $type = null){
		if($message == null){
			parent::__construct('Operation failed.');
		}else{
			parent::__construct($message);
		}
	}
}

class YaffmapLoggedException extends YaffmapException{
	
	public function __construct($message = null, $type = null){
		$error = new ErrorLog();
		$error->setRequest(Kobold::dump_ret($_REQUEST));
		$error->setMessage($message);
		$error->setIp($_SERVER['REMOTE_ADDR']);
		if($type == null){
			$error->setType(ErrorLogPeer::TYPE_EXCEPTION);
		}
		$error->save();
		parent::__construct($message, $type);
	}
}

class EMalformedJson extends YaffmapLoggedException{
	
	public function __construct(){
		parent::__construct('JSON string parsing error.', ErrorLogPeer::TYPE_JSON);
	}
}

class EUnknownAttribute extends YaffmapLoggedException{
	
	public function __construct($attribute){
		parent::__construct('Unknown attribute "'.$attribute.'" given.');
	}
}

class EIsufficientQuery extends YaffmapLoggedException{
	
	public function __construct($message = null){
		parent::__construct('Isufficient query given: '.$message);
	}
}

class EUnknownRequestElement extends YaffmapLoggedException{
	// thrown, when not supported element given in do=getFrontendData
	public function __construct($message){
		parent::__construct('Unknown request element given: '.$message);
	}
}

class EAddrMissing extends EIsufficientQuery{
	
	public function __construct(){
		parent::__construct('Mac- or ipv4/6-address is missing.');
	}
}

class EInvalidIpAddr extends YaffmapLoggedException{
	
	public function __construct($ip){
		parent::__construct('Invalid ip address "'.$ip.'" given.');
	}
}

class YaffmapSoapException extends YaffmapException{
	
	public function __construct($msg){
		parent::__construct($msg, ErrorLogPeer::TYPE_SOAP);
	}
}