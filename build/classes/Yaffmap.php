<?php
require_once 'YaffmapException.php';
require_once 'YaffmapResponse.php';
require_once 'YaffmapGetUpgrade.php';
require_once 'YaffmapGlobalUpdate.php';
require_once 'YaffmapNodeUpdate.php';
require_once 'YaffmapGetID.php';
require_once 'YaffmapGetFilter.php';
require_once 'YaffmapGetConfig.php';
require_once 'YaffmapNewAgentRelease.php';
require_once 'YaffmapGetFrontendData.php';

class Yaffmap{
	
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
		$this->allowed = array('do');
//		$this->checkAgentCompatibility();
	}
	
	public function checkInput($allowed){
		$this->allowed = array_merge($this->allowed, $allowed);
		Yaffmap::checkRequestArray($this->request, $this->allowed); // check request string for illegal stuff
	}
	
	public function checkAgentCompatibility(){
		$versionMapping = VersionMappingAgentQuery::create()
			->filterByAgentRelease($this->request['release'])
			->filterByAgentSubRelease($this->request['subRelease'])
			->filterByAgentUpgradeTree($this->request['tree'])
			->filterByAgentVersion($this->request['version'])
			->filterByBackendRelease(YaffmapConfig::get('version'))
			->count();
		if($versionMapping == 0){
			throw new YaffmapException('agent('.$this->request['release'].'-'.$this->request['subRelease'].'_'.$this->request['tree'].'_'.$this->request['version'].') is not compatible to backend('.YaffmapConfig::get('version').').');
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
	 * @deprecated
	 */
	public static function dump($var){
		echo '<pre>'.print_r($var, true).'</pre>';
	}
	
	/**
	 * @deprecated
	 */
	public static function dump_ret($var){
		$ret = null;
		ob_start();
		var_dump($var);
		$ret = ob_get_clean();
		ob_end_clean();
		return $ret;
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
		$log->setRequestString(Yaffmap::dump_ret($_REQUEST));
		$log->setRequest($_REQUEST['do']);
		$log->save();
	}
	
//	public function getResponse(){
//		return $this->response;
//	}
}
?>