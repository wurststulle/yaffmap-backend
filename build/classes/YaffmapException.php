<?php
class YaffmapException extends Exception{
	
	const SEVERITY_DEFAULT = '0';
	const SEVERITY_CRITICAL = '1';
	
	protected $severity;
	
	public function __construct($message = null, $severity = self::SEVERITY_DEFAULT){
		$this->severity = $severity;
		if($message == null){
			parent::__construct('Operation failed.');
		}else{
			parent::__construct($message);
		}
	}
}

class YaffmapLoggedException extends YaffmapException{
	
	public function __construct($message = null, $severity = self::SEVERITY_DEFAULT){
		$error = new ErrorLog();
		$error->setRequest(Kobold::dump_ret($_REQUEST));
		$error->setMessage($message);
		$error->setIp($_SERVER['REMOTE_ADDR']);
		$error->save();
		parent::__construct($message, $severity);
	}
}

class EMalformedJson extends YaffmapLoggedException{
	
	public function __construct($severity = self::SEVERITY_DEFAULT){
		parent::__construct('JSON string parsing error.', $severity);
	}
}

class EUnknownAttribute extends YaffmapLoggedException{
	
	public function __construct($attribute, $severity = self::SEVERITY_DEFAULT){
		parent::__construct('Unknown attribute "'.$attribute.'" given.', $severity);
	}
}

class EIsufficientQuery extends YaffmapLoggedException{
	
	public function __construct($message = null, $severity = self::SEVERITY_DEFAULT){
		parent::__construct('Isufficient query given: '.$message, $severity);
	}
}

class EUnknownRequestElement extends YaffmapLoggedException{
	// thrown, when not supported element given in do=getFrontendData
	public function __construct($message, $severity = self::SEVERITY_DEFAULT){
		parent::__construct('Unknown request element given: '.$message, $severity);
	}
}

class EAddrMissing extends EIsufficientQuery{
	
	public function __construct($severity = self::SEVERITY_DEFAULT){
		parent::__construct('Mac- or ipv4/6-address is missing.', $severity);
	}
}

class EInvalidIpAddr extends YaffmapLoggedException{
	
	public function __construct($ip, $severity = self::SEVERITY_DEFAULT){
		parent::__construct('Invalid ip address "'.$ip.'" given.', $severity);
	}
}

class YaffmapSoapException extends YaffmapException{
	
	public function __construct($msg, $severity = self::SEVERITY_DEFAULT){
		parent::__construct($msg, $severity);
	}
}