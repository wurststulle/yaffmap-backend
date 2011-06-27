<?php
require_once 'YaffmapException.php';
require_once 'YaffmapResponse.php';
require_once 'YaffmapGetUpgrade.php';
require_once 'YaffmapGlobalUpdate.php';
require_once 'YaffmapNodeUpdate.php';
require_once 'YaffmapGetID.php';
require_once 'YaffmapNewAgentRelease.php';
require_once 'YaffmapConfig.php';

class Yaffmap{
	
	/**
	 * @var YaffmapResponse
	 */
	protected $response = null;
	protected $request = null;
	
	/**
	 * allowed keys in request
	 * @var array
	 */
	protected $allowed = null;
	
	public function __construct($request = null, $response = null){
		if($request == null){
			$this->request = $_REQUEST;
		}else{
			$this->request = $request;
		}
		if($response == null){
			$this->response = new YaffmapResponse();
		}else{
			$this->response = $response;
		}
		$this->allowed = array('do', 'release', 'tree', 'version');
	}
	
	/**
	 * @param array $allowed
	 * @param boolean $skipElementCheck disable check for 'release', 'tree', 'version' and 'do' in REQUEST elements
	 * @throws YaffmapException
	 */
	public function checkInput($allowed = null, $skipElementCheck = false, $skipCompatibilityCheck = false){
		if($allowed != null){
			$this->allowed = array_merge($this->allowed, $allowed);
		}
		if(!$skipElementCheck){
			if(!array_key_exists('release', $this->request) 
				|| !array_key_exists('tree', $this->request)
				|| !array_key_exists('version', $this->request)
				|| !array_key_exists('do', $this->request)){
				throw new YaffmapException('basic query element missing.', YaffmapException::SEVERITY_CRITICAL);	
			}
		}
		Yaffmap::checkRequestArray($this->request, $this->allowed); // check request string for illegal stuff
		if(!$skipCompatibilityCheck){
			$this->checkAgentCompatibility();
		}
		
	}
	
	public function checkAgentCompatibility($skipCheck = false){
		$splitRelease = explode("-", $this->request['release']);
		$versionMapping = VersionMappingAgentQuery::create()
			->filterByAgentRelease($splitRelease[0])
			->filterByAgentSubRelease($splitRelease[1])
			->filterByAgentUpgradeTree($this->request['tree'])
			->filterByAgentVersion($this->request['version'])
			->filterByBackendRelease(YaffmapConfig::get('version'))
			->count();
		if($versionMapping == 0){
			throw new YaffmapException('agent('.$splitRelease[0].'-'.$splitRelease[1].'_'.$this->request['tree'].'_'.$this->request['version'].') is not compatible to backend('.YaffmapConfig::get('version').').', YaffmapException::SEVERITY_CRITICAL);
		}
	}
	
	/**
	 * decode json string
	 * 
	 * @param object $jsonString json string to be decoded
	 * @return object made out of json string
	 * @throws EMalformedJson
	 */
	public static function decodeJson($jsonString){
		$obj = json_decode(str_replace('\"','"',$jsonString));
		if(json_last_error() != 0){
			throw new EMalformedJson();
		}else{
			return $obj;
		}
	}
	
	/**
	 * check that no more elements are in $request then in $allowed
	 * 
	 * @param array $request
	 * @param array $allowed
	 * @throws EUnknownRequestElement
	 */
	public static function checkRequestArray($request, $allowed){
		foreach($request as $key => $value){
			if(!in_array($key, $allowed)){
				throw new EUnknownRequestElement($key);
			}
		}
	}
	
	public static function logAccess($debug = null){
		$log = new AccessLog();
		$log->setIp($_SERVER['REMOTE_ADDR']);
		$log->setDebug($debug);
		$log->setRequestString(Kobold::dump_ret($_REQUEST));
		$log->setRequest($_REQUEST['do']);
		$log->save();
	}
}
?>